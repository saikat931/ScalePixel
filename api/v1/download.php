<?php
/**
 * ScalePixel - Direct File Download Endpoint
 * Forces immediate browser file download with Content-Disposition: attachment
 */

// Enable CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, HEAD, OPTIONS');
header('Access-Control-Allow-Headers: *');
header('Access-Control-Expose-Headers: Content-Length, Content-Type, Content-Disposition');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

$file = $_GET['file'] ?? '';
if (empty($file)) {
    http_response_code(400);
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Missing file parameter.']);
    exit;
}

// Security: Prevent directory traversal attacks
$cleanFilename = basename(parse_url($file, PHP_URL_PATH));
$cleanFilename = preg_replace('/[^a-zA-Z0-9_.-]/', '', $cleanFilename);

if (empty($cleanFilename)) {
    http_response_code(400);
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Invalid filename.']);
    exit;
}

$storageDir = realpath(__DIR__ . '/../../storage/temp/');
$filePath = $storageDir . DIRECTORY_SEPARATOR . $cleanFilename;

// Ensure path stays strictly inside storage/temp/
if (!$storageDir || !file_exists($filePath) || strpos(realpath($filePath), $storageDir) !== 0) {
    http_response_code(404);
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'File not found or expired.']);
    exit;
}

// Determine mime type
$ext = strtolower(pathinfo($cleanFilename, PATHINFO_EXTENSION));
$mimeTypes = [
    'jpg'  => 'image/jpeg',
    'jpeg' => 'image/jpeg',
    'png'  => 'image/png',
    'webp' => 'image/webp',
    'avif' => 'image/avif',
    'gif'  => 'image/gif',
    'pdf'  => 'application/pdf',
];
$mime = $mimeTypes[$ext] ?? 'application/octet-stream';

// Send direct download headers
header('Content-Description: File Transfer');
header('Content-Type: ' . $mime);
header('Content-Disposition: attachment; filename="' . $cleanFilename . '"');
header('Content-Transfer-Encoding: binary');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Pragma: public');
header('Content-Length: ' . filesize($filePath));

// Clean output buffer to prevent corrupted file stream
if (ob_get_level()) {
    ob_end_clean();
}

readfile($filePath);
exit;
