<?php
/**
 * ScalePixel - Core Security & Anti-Hacking Hardening Layer
 * 
 * Protects against:
 * 1. Web Shell & Polyglot Uploads
 * 2. Decompression Bombs & Pixel Flood DoS
 * 3. Path Traversal & Double-Extension Attacks
 * 4. Timing Attacks on Secret Keys
 * 5. Denial-of-Service (IP Rate Limiting)
 */

class ScalePixelSecurity {

    // Maximum permitted dimensions to prevent decompression bombs
    const MAX_DIMENSION = 10000;
    // Maximum file upload size: 35 MB
    const MAX_FILE_SIZE = 36700160;

    /**
     * Deep inspection of uploaded image file
     */
    public static function inspectUploadedFile($filePath) {
        if (!file_exists($filePath)) {
            throw new Exception("Security Alert: Uploaded file missing from disk.");
        }

        $fileSize = filesize($filePath);
        if ($fileSize === 0) {
            throw new Exception("Security Alert: Empty file uploaded.");
        }
        if ($fileSize > self::MAX_FILE_SIZE) {
            throw new Exception("Security Alert: File size exceeds the 35 MB security threshold.");
        }

        // 1. Magic-byte verification via finfo (cannot be spoofed by client header)
        if (class_exists('finfo')) {
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $realMime = $finfo->file($filePath);
            $allowedMimes = [
                'image/jpeg', 'image/jpg', 'image/png', 'image/webp',
                'image/avif', 'image/gif', 'image/bmp', 'image/x-ms-bmp'
            ];

            if (!in_array($realMime, $allowedMimes)) {
                throw new Exception("Security Alert: Malicious file format detected. Magic-bytes indicate {$realMime}, which is prohibited.");
            }
        }

        // 2. Decode test & Decompression Bomb Defense
        $imageInfo = @getimagesize($filePath);
        if (!$imageInfo) {
            throw new Exception("Security Alert: File failed integrity check. Not a valid decodable image.");
        }

        $width = $imageInfo[0];
        $height = $imageInfo[1];

        if ($width > self::MAX_DIMENSION || $height > self::MAX_DIMENSION) {
            throw new Exception("Security Alert: Image dimensions ({$width}x{$height}) exceed maximum allowed dimension of " . self::MAX_DIMENSION . "px (Decompression Bomb Protection).");
        }

        // 3. Scan for executable script signatures in the first 8 KB
        $handle = fopen($filePath, 'rb');
        $header = fread($handle, 8192);
        fclose($handle);

        $dangerousSignatures = [
            '<?php', '<?=', '<script', 'eval(', 'base64_decode(', 
            'system(', 'exec(', 'shell_exec(', 'passthru(', 'proc_open'
        ];

        foreach ($dangerousSignatures as $sig) {
            if (stripos($header, $sig) !== false) {
                throw new Exception("Security Alert: Malicious executable signature detected inside file header.");
            }
        }

        return true;
    }

    /**
     * Sanitize user-provided filename prefix to prevent Path Traversal
     */
    public static function sanitizePrefix($prefix) {
        // Only alphanumeric, hyphens and underscores allowed; max 32 chars
        $clean = preg_replace('/[^a-zA-Z0-9_-]/', '', $prefix);
        $clean = substr($clean, 0, 32);
        return empty($clean) ? 'media' : $clean;
    }

    /**
     * IP Rate Limiter to prevent DoS (60 requests / minute)
     */
    public static function checkRateLimit($limitPerMinute = 60) {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $ipHash = md5($ip);
        $cacheFile = sys_get_temp_dir() . '/sp_rate_' . $ipHash . '.json';

        $now = time();
        $history = [];

        if (file_exists($cacheFile)) {
            $data = json_decode(@file_get_contents($cacheFile), true);
            if (is_array($data)) {
                // Filter requests in the last 60 seconds
                $history = array_filter($data, function($t) use ($now) {
                    return ($now - $t) < 60;
                });
            }
        }

        if (count($history) >= $limitPerMinute) {
            throw new Exception("Rate limit exceeded. Maximum {$limitPerMinute} requests per minute allowed per IP.");
        }

        $history[] = $now;
        @file_put_contents($cacheFile, json_encode($history));
    }

    /**
     * Timing-attack resistant string comparison for API Secrets
     */
    public static function timingSafeEquals($known, $given) {
        return hash_equals((string)$known, (string)$given);
    }
}

