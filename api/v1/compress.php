<?php
/**
 * ScalePixel REST API - /api/v1/compress
 * High-speed image compression & format optimization
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-ScalePixel-Key, X-ScalePixel-Secret, X-API-Key, X-API-Secret');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed. Use POST.']);
    exit;
}

require_once __DIR__ . '/../../includes/Security.php';
require_once __DIR__ . '/../../includes/ImageProcessor.php';
require_once __DIR__ . '/../../includes/Auth.php';

// IP Rate Limiting (Anti-DoS Protection)
try {
    ScalePixelSecurity::checkRateLimit(120);
} catch (Exception $e) {
    http_response_code(429);
    echo json_encode(['status' => 'error', 'code' => 'RATE_LIMITED', 'message' => $e->getMessage()]);
    exit;
}

// Authenticate request
$auth = ScalePixelAuth::authenticate(true);
if (!$auth['authenticated']) {
    http_response_code(401);
    echo json_encode([
        'status'  => 'error',
        'code'    => 'UNAUTHORIZED',
        'message' => $auth['error'],
    ]);
    exit;
}

// Check uploaded file or payload
$tempSourceFile = null;
$originalFileName = 'upload';

if (!empty($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
    $tempSourceFile = $_FILES['file']['tmp_name'];
    $originalFileName = $_FILES['file']['name'];
} elseif (!empty($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $tempSourceFile = $_FILES['image']['tmp_name'];
    $originalFileName = $_FILES['image']['name'];
} elseif (!empty($_POST['image_base64'])) {
    // Handle base64 string
    $base64Data = $_POST['image_base64'];
    if (preg_match('/^data:image\/(\w+);base64,/', $base64Data, $type)) {
        $base64Data = substr($base64Data, strpos($base64Data, ',') + 1);
        $ext = strtolower($type[1]);
    } else {
        $ext = 'jpg';
    }
    $decoded = base64_decode($base64Data);
    if ($decoded === false) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Invalid base64 image data.']);
        exit;
    }
    $tempSourceFile = tempnam(sys_get_temp_dir(), 'sp_in_') . '.' . $ext;
    file_put_contents($tempSourceFile, $decoded);
    $originalFileName = "base64_upload.{$ext}";
}

if (!$tempSourceFile || !file_exists($tempSourceFile)) {
    http_response_code(400);
    echo json_encode([
        'status'  => 'error',
        'code'    => 'NO_FILE_UPLOADED',
        'message' => 'No valid image file found in request. Send multipart/form-data with key "file" or "image".'
    ]);
    exit;
}

// Deep Anti-Hacking Security Inspection (Magic-Bytes, Decompression Bombs & Polyglot Scans)
try {
    ScalePixelSecurity::inspectUploadedFile($tempSourceFile);
} catch (Exception $e) {
    if ($tempSourceFile && file_exists($tempSourceFile) && strpos($tempSourceFile, 'sp_in_') !== false) {
        @unlink($tempSourceFile);
    }
    http_response_code(400);
    echo json_encode([
        'status'  => 'error',
        'code'    => 'SECURITY_VIOLATION',
        'message' => $e->getMessage()
    ]);
    exit;
}

// Extraction options
$prefix = ScalePixelSecurity::sanitizePrefix($_POST['prefix'] ?? 'media');
$mode = trim($_POST['mode'] ?? 'balanced'); // avatar, showcase, balanced, max, lossless
$format = strtolower(trim($_POST['format'] ?? 'auto'));
$quality = isset($_POST['quality']) ? (int) $_POST['quality'] : 80;
$width = !empty($_POST['width']) ? (int) $_POST['width'] : null;
$height = !empty($_POST['height']) ? (int) $_POST['height'] : null;
$fit = trim($_POST['fit'] ?? 'cover');
$includeSeconds = isset($_POST['include_seconds']) ? filter_var($_POST['include_seconds'], FILTER_VALIDATE_BOOLEAN) : true;

// Auto format resolution based on mode
if ($mode === 'avatar') {
    $format = 'jpeg';
    $quality = 80;
    if (!$width && !$height) {
        $width = 600;
        $height = 600;
    }
    $fit = 'cover';
} elseif ($mode === 'showcase') {
    $format = 'webp';
    $quality = 75;
} elseif ($format === 'auto') {
    // If not specified, default to webp or keep original format
    $origExt = strtolower(pathinfo($originalFileName, PATHINFO_EXTENSION));
    $format = ($origExt === 'png') ? 'webp' : 'jpeg';
}

$outputExt = ($format === 'jpeg' || $format === 'jpg') ? 'jpg' : $format;
$finalFilename = ImageProcessor::generateFilename($prefix, $outputExt, $includeSeconds);

$storageDir = __DIR__ . '/../../storage/temp/';
if (!is_dir($storageDir)) {
    mkdir($storageDir, 0755, true);
}
$outputPath = $storageDir . $finalFilename;

try {
    $result = ImageProcessor::process($tempSourceFile, $outputPath, [
        'format'  => $format,
        'quality' => $quality,
        'width'   => $width,
        'height'  => $height,
        'fit'     => $fit,
        'mode'    => $mode,
    ]);

    // Track usage stats
    if (!empty($auth['source_key']) && $auth['source_key'] !== 'web_studio_session') {
        ScalePixelAuth::recordUsage($auth['source_key'], $result['saved_bytes']);
    }

    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
    $host = $_SERVER['HTTP_HOST'];
    $scriptDir = dirname(dirname(dirname($_SERVER['SCRIPT_NAME'])));
    $scriptDir = rtrim(str_replace('\\', '/', $scriptDir), '/');
    $baseUrl = "{$protocol}{$host}{$scriptDir}";
    $downloadUrl = "{$baseUrl}/storage/temp/{$finalFilename}";

    echo json_encode([
        'status'         => 'success',
        'message'        => 'Image compressed and optimized successfully.',
        'data'           => [
            'filename'         => $finalFilename,
            'download_url'     => $downloadUrl,
            'original_name'    => $originalFileName,
            'original_size_kb' => round($result['original_size'] / 1024, 2),
            'compressed_size_kb' => round($result['compressed_size'] / 1024, 2),
            'saved_bytes'      => $result['saved_bytes'],
            'saved_percent'    => $result['saved_percent'],
            'original_dimensions' => "{$result['original_width']}x{$result['original_height']}",
            'new_dimensions'      => "{$result['new_width']}x{$result['new_height']}",
            'format'           => $result['output_format'],
            'mode'             => $mode,
            'execution_time_ms'=> $result['execution_time_ms'],
        ],
        'meta'           => [
            'client'           => $auth['key_data']['name'] ?? 'ScalePixel Studio',
            'plan'             => $auth['key_data']['plan'] ?? 'Free',
            'engine'           => 'ScalePixel Quantum Engine v1.0',
            'timestamp'        => date('Y-m-d H:i:s'),
        ]
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status'  => 'error',
        'code'    => 'PROCESSING_FAILED',
        'message' => $e->getMessage()
    ]);
}
exit;
