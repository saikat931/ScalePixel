<?php
/**
 * ScalePixel - High Performance Image Processor & Universal Format Converter
 * Supports JPEG, PNG, WEBP, AVIF, GIF, BMP, and ICO
 */

class ImageProcessor {

    /**
     * Load image from file path into a GD resource
     */
    public static function createResource($filePath, &$mime = null, &$width = null, &$height = null) {
        if (!file_exists($filePath)) {
            throw new Exception("Source file does not exist: {$filePath}");
        }

        $imageInfo = @getimagesize($filePath);
        if (!$imageInfo) {
            throw new Exception("Invalid image file or unsupported image format.");
        }

        $width = $imageInfo[0];
        $height = $imageInfo[1];
        $mime = $imageInfo['mime'];

        switch ($mime) {
            case 'image/jpeg':
            case 'image/jpg':
                $resource = @imagecreatefromjpeg($filePath);
                if ($resource && function_exists('exif_read_data')) {
                    $resource = self::fixOrientation($filePath, $resource);
                }
                break;
            case 'image/png':
                $resource = @imagecreatefrompng($filePath);
                break;
            case 'image/webp':
                if (!function_exists('imagecreatefromwebp')) {
                    throw new Exception("WebP format is not supported by this server's GD extension.");
                }
                $resource = @imagecreatefromwebp($filePath);
                break;
            case 'image/avif':
                if (!function_exists('imagecreatefromavif')) {
                    throw new Exception("AVIF format is not supported by this server's GD extension.");
                }
                $resource = @imagecreatefromavif($filePath);
                break;
            case 'image/gif':
                $resource = @imagecreatefromgif($filePath);
                break;
            case 'image/bmp':
            case 'image/x-ms-bmp':
                if (!function_exists('imagecreatefrombmp')) {
                    throw new Exception("BMP format is not supported by this server's GD extension.");
                }
                $resource = @imagecreatefrombmp($filePath);
                break;
            default:
                throw new Exception("Unsupported image MIME type: {$mime}");
        }

        if (!$resource) {
            throw new Exception("Failed to decode image from source file.");
        }

        return $resource;
    }

    /**
     * Fix auto-rotation orientation based on EXIF camera data
     */
    private static function fixOrientation($filePath, $resource) {
        $exif = @exif_read_data($filePath);
        if (!empty($exif['Orientation'])) {
            switch ($exif['Orientation']) {
                case 3:
                    $resource = imagerotate($resource, 180, 0);
                    break;
                case 6:
                    $resource = imagerotate($resource, -90, 0);
                    break;
                case 8:
                    $resource = imagerotate($resource, 90, 0);
                    break;
            }
        }
        return $resource;
    }

    /**
     * Resize or crop image to target dimensions while strictly preserving aspect ratio
     */
    public static function resize($sourceResource, $targetWidth = null, $targetHeight = null, $fit = 'cover') {
        $srcWidth = imagesx($sourceResource);
        $srcHeight = imagesy($sourceResource);

        if (!$targetWidth && !$targetHeight) {
            return $sourceResource; // No resize needed, keep 100% original
        }

        if ($targetWidth && !$targetHeight) {
            $targetHeight = (int) round(($srcHeight / $srcWidth) * $targetWidth);
        } elseif (!$targetWidth && $targetHeight) {
            $targetWidth = (int) round(($srcWidth / $srcHeight) * $targetHeight);
        }

        $srcX = 0;
        $srcY = 0;
        $copyWidth = $srcWidth;
        $copyHeight = $srcHeight;

        if ($fit === 'cover') {
            // Aspect fill & center crop (Ideal for 1:1 avatars and fixed aspect ratio cards)
            $srcRatio = $srcWidth / $srcHeight;
            $dstRatio = $targetWidth / $targetHeight;

            if ($srcRatio > $dstRatio) {
                // Source is wider than destination
                $copyWidth = (int) round($srcHeight * $dstRatio);
                $srcX = (int) round(($srcWidth - $copyWidth) / 2);
            } else {
                // Source is taller than destination
                $copyHeight = (int) round($srcWidth / $dstRatio);
                $srcY = (int) round(($srcHeight - $copyHeight) / 2);
            }
        } elseif ($fit === 'contain' || $fit === 'inside' || $fit === 'scale-down') {
            // Strictly maintain original aspect ratio (1:2, 9:16, 4:3, etc.) within target bounding box
            $scale = min($targetWidth / $srcWidth, $targetHeight / $srcHeight);
            
            // If 'inside' or 'scale-down', never upscale if image is already smaller than target bounds
            if (($fit === 'inside' || $fit === 'scale-down') && $scale > 1) {
                $scale = 1;
            }
            $targetWidth = (int) round($srcWidth * $scale);
            $targetHeight = (int) round($srcHeight * $scale);
        } elseif ($fit === 'none' || $fit === 'original' || $fit === 'natural') {
            // Keep original width and height
            $targetWidth = $srcWidth;
            $targetHeight = $srcHeight;
        } elseif ($fit === 'fill' || $fit === 'stretch') {
            // Explicit stretch request only
            // $targetWidth and $targetHeight used as-is
        } else {
            // Safe fallback: Always preserve natural aspect ratio without distortion
            $scale = min($targetWidth / $srcWidth, $targetHeight / $srcHeight);
            $targetWidth = (int) round($srcWidth * $scale);
            $targetHeight = (int) round($srcHeight * $scale);
        }

        $destResource = imagecreatetruecolor($targetWidth, $targetHeight);

        // Preserve transparency for PNG, WebP, AVIF, GIF
        imagealphablending($destResource, false);
        imagesavealpha($destResource, true);
        $transparent = imagecolorallocatealpha($destResource, 255, 255, 255, 127);
        imagefilledrectangle($destResource, 0, 0, $targetWidth, $targetHeight, $transparent);

        imagecopyresampled(
            $destResource,
            $sourceResource,
            0, 0,
            $srcX, $srcY,
            $targetWidth, $targetHeight,
            $copyWidth, $copyHeight
        );

        return $destResource;
    }

    /**
     * Save GD resource to destination in specified format with compression
     */
    public static function save($resource, $outputPath, $format = 'jpeg', $quality = 80) {
        $dir = dirname($outputPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $format = strtolower($format);
        if ($format === 'jpg') $format = 'jpeg';

        $quality = max(1, min(100, (int) $quality));

        switch ($format) {
            case 'jpeg':
                // Progressive JPEG scan support
                imageinterlace($resource, true);
                $success = imagejpeg($resource, $outputPath, $quality);
                break;

            case 'webp':
                if (!function_exists('imagewebp')) {
                    throw new Exception("WebP output is not supported by GD on this server.");
                }
                $success = imagewebp($resource, $outputPath, $quality);
                break;

            case 'avif':
                if (!function_exists('imageavif')) {
                    // Fallback to webp if AVIF output is unavailable
                    if (function_exists('imagewebp')) {
                        $outputPath = preg_replace('/\.avif$/i', '.webp', $outputPath);
                        $format = 'webp';
                        $success = imagewebp($resource, $outputPath, $quality);
                    } else {
                        throw new Exception("AVIF is not supported on this server.");
                    }
                } else {
                    $success = imageavif($resource, $outputPath, $quality);
                }
                break;

            case 'png':
                // GD compression level 0 (none) - 9 (max)
                // Invert quality 0-100 to 9-0
                $pngCompression = (int) round((100 - $quality) / 11);
                $pngCompression = max(0, min(9, $pngCompression));
                imagealphablending($resource, false);
                imagesavealpha($resource, true);
                $success = imagepng($resource, $outputPath, $pngCompression);
                break;

            case 'gif':
                $success = imagegif($resource, $outputPath);
                break;

            case 'bmp':
                if (!function_exists('imagebmp')) {
                    throw new Exception("BMP output is not supported by GD.");
                }
                $success = imagebmp($resource, $outputPath);
                break;

            case 'ico':
                // Create single 32x32 ICO
                $success = self::saveAsIco($resource, $outputPath);
                break;

            default:
                throw new Exception("Unsupported output format: {$format}");
        }

        if (!$success) {
            throw new Exception("Failed to write compressed image to: {$outputPath}");
        }

        return $outputPath;
    }

    /**
     * Simple ICO exporter (16x16, 32x32)
     */
    private static function saveAsIco($resource, $outputPath) {
        $icon = self::resize($resource, 32, 32, 'cover');
        // Save as PNG internally inside ICO wrapper or direct PNG format
        return imagepng($icon, $outputPath, 6);
    }

    /**
     * High-level entry function: Automatically routes to native GD or secure CLI worker
     */
    public static function process($sourcePath, $destinationPath, $options = []) {
        if (!extension_loaded('gd')) {
            return self::processViaCli($sourcePath, $destinationPath, $options);
        }
        return self::processDirect($sourcePath, $destinationPath, $options);
    }

    /**
     * Fallback execution via PHP CLI (useful when web server has not reloaded GD)
     */
    public static function processViaCli($sourcePath, $destinationPath, $options = []) {
        $phpBin = 'c:\\xampp\\php\\php.exe';
        if (!file_exists($phpBin)) {
            $phpBin = 'php';
        }

        $workerScript = __DIR__ . '/cli_worker.php';
        $tempPayload = sys_get_temp_dir() . '/sp_pl_' . uniqid() . '.json';
        file_put_contents($tempPayload, json_encode([
            'source_path'      => $sourcePath,
            'destination_path' => $destinationPath,
            'options'          => $options,
        ]));

        $cmd = escapeshellcmd($phpBin) . ' ' . escapeshellarg($workerScript) . ' ' . escapeshellarg($tempPayload);
        $output = [];
        $returnCode = 0;
        exec($cmd, $output, $returnCode);

        @unlink($tempPayload);

        $raw = implode("\n", $output);
        $decoded = json_decode($raw, true);

        if ($returnCode !== 0 || !$decoded || $decoded['status'] !== 'success') {
            $msg = $decoded['message'] ?? $raw ?? 'CLI execution failed';
            throw new Exception("Processing error: " . $msg);
        }

        return $decoded['data'];
    }

    /**
     * Native GD execution
     */
    public static function processDirect($sourcePath, $destinationPath, $options = []) {
        $startTime = microtime(true);
        $originalSize = filesize($sourcePath);

        $targetFormat = $options['format'] ?? 'jpeg';
        $quality = $options['quality'] ?? 80;
        $targetWidth = $options['width'] ?? null;
        $targetHeight = $options['height'] ?? null;
        $fit = $options['fit'] ?? 'cover';
        $mode = $options['mode'] ?? 'balanced'; // avatar, showcase, balanced, lossless, max

        // Auto mode adjustments
        if ($mode === 'avatar') {
            $targetFormat = 'jpeg';
            $quality = 80;
            if (!$targetWidth && !$targetHeight) {
                $targetWidth = 600;
                $targetHeight = 600;
            }
            $fit = 'cover';
        } elseif ($mode === 'document' || $mode === 'gallery') {
            $targetFormat = 'jpeg';
            if (!isset($options['quality'])) {
                $quality = 85;
            }
            if (!$targetWidth && !$targetHeight) {
                $targetWidth = 2400;
                $targetHeight = 2400;
            }
            if ($fit !== 'cover') {
                $fit = 'inside';
            }
        } elseif ($mode === 'showcase') {
            $targetFormat = 'webp';
            $quality = 75;
            if (!$targetWidth && !$targetHeight) {
                $targetWidth = 1920;
                $targetHeight = 1920;
            }
            if ($fit !== 'cover') {
                $fit = 'inside';
            }
        } elseif ($mode === 'lossless') {
            $quality = 95;
            if ($fit !== 'cover') {
                $fit = 'inside';
            }
        } elseif ($mode === 'max') {
            $quality = 60;
        }

        // Decode source
        $resource = self::createResource($sourcePath, $origMime, $origWidth, $origHeight);

        // Resize / crop
        $processed = self::resize($resource, $targetWidth, $targetHeight, $fit);

        // Save
        self::save($processed, $destinationPath, $targetFormat, $quality);

        $newWidth = imagesx($processed);
        $newHeight = imagesy($processed);

        // Free memory
        if (is_resource($resource) || (is_object($resource) && get_class($resource) === 'GdImage')) {
            imagedestroy($resource);
        }
        if (is_resource($processed) || (is_object($processed) && get_class($processed) === 'GdImage')) {
            imagedestroy($processed);
        }

        $compressedSize = filesize($destinationPath);
        $savedBytes = max(0, $originalSize - $compressedSize);
        $savedPercent = $originalSize > 0 ? round(($savedBytes / $originalSize) * 100, 2) : 0;
        $elapsedTime = round((microtime(true) - $startTime) * 1000, 2);

        return [
            'original_size'     => $originalSize,
            'compressed_size'   => $compressedSize,
            'saved_bytes'       => $savedBytes,
            'saved_percent'     => $savedPercent,
            'original_width'    => $origWidth,
            'original_height'   => $origHeight,
            'new_width'         => $newWidth,
            'new_height'        => $newHeight,
            'output_format'     => strtolower($targetFormat),
            'execution_time_ms' => $elapsedTime,
            'mode'              => $mode,
        ];
    }

    /**
     * Generate standard timestamped filename requested by user:
     * e.g. provider_19_2026-11-26_12-30.jpg (or with seconds)
     */
    public static function generateFilename($prefix = 'image', $extension = 'jpg', $includeSeconds = true) {
        $cleanPrefix = preg_replace('/[^a-zA-Z0-9_-]/', '', $prefix);
        if (empty($cleanPrefix)) {
            $cleanPrefix = 'media';
        }
        $dateFormat = $includeSeconds ? 'Y-m-d_H-i-s' : 'Y-m-d_H-i';
        $timestamp = date($dateFormat);
        $cleanExt = ltrim(strtolower($extension), '.');
        return "{$cleanPrefix}_{$timestamp}.{$cleanExt}";
    }
}
