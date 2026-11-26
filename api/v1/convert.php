<?php
/**
 * ScalePixel REST API - /api/v1/convert
 * Universal Format Converter (JPG, PNG, WEBP, AVIF, GIF, BMP, ICO)
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

$tempSourceFile = null;
$originalFileName = 'upload';

if (!empty($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
    $tempSourceFile = $_FILES['file']['tmp_name'];
    $originalFileName = $_FILES['file']['name'];
} elseif (!empty($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $tempSourceFile = $_FILES['image']['tmp_name'];
    $originalFileName = $_FILES['image']['name'];
}

if (!$tempSourceFile || !file_exists($tempSourceFile)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'No file uploaded for conversion.']);
    exit;
}

// Deep Anti-Hacking Security Inspection
try {
    ScalePixelSecurity::inspectUploadedFile($tempSourceFile);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'status'  => 'error',
        'code'    => 'SECURITY_VIOLATION',
        'message' => $e->getMessage()
    ]);
    exit;
}

$targetFormat = strtolower(trim($_POST['to'] ?? $_POST['format'] ?? 'webp'));
$quality = isset($_POST['quality']) ? (int) $_POST['quality'] : 85;
$prefix = ScalePixelSecurity::sanitizePrefix($_POST['prefix'] ?? pathinfo($originalFileName, PATHINFO_FILENAME));

$outputExt = ($targetFormat === 'jpeg' || $targetFormat === 'jpg') ? 'jpg' : $targetFormat;
$finalFilename = ImageProcessor::generateFilename($prefix, $outputExt, true);

$storageDir = __DIR__ . '/../../storage/temp/';
if (!is_dir($storageDir)) {
    mkdir($storageDir, 0755, true);
}
$outputPath = $storageDir . $finalFilename;

try {
    $result = ImageProcessor::process($tempSourceFile, $outputPath, [
        'format'  => $targetFormat,
        'quality' => $quality,
        'mode'    => 'convert'
    ]);

    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
    $host = $_SERVER['HTTP_HOST'];
    $scriptDir = dirname(dirname(dirname($_SERVER['SCRIPT_NAME'])));
    $scriptDir = rtrim(str_replace('\\', '/', $scriptDir), '/');
    $baseUrl = "{$protocol}{$host}{$scriptDir}";
    $downloadUrl = "{$baseUrl}/storage/temp/{$finalFilename}";

    echo json_encode([
        'status'       => 'success',
        'message'      => "Image converted to {$targetFormat} successfully.",
        'data'         => [
            'filename'         => $finalFilename,
            'download_url'     => $downloadUrl,
            'target_format'    => $targetFormat,
            'original_name'    => $originalFileName,
            'original_size_kb' => round($result['original_size'] / 1024, 2),
            'converted_size_kb'=> round($result['compressed_size'] / 1024, 2),
            'saved_percent'    => $result['saved_percent'],
            'execution_time_ms'=> $result['execution_time_ms'],
        ]
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
exit;
