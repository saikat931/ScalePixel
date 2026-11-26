<?php
/**
 * ScalePixel REST API - /api/v1/keys
 * API Key management, generation, and quota checks
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-ScalePixel-Key, X-ScalePixel-Secret');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/../../includes/Auth.php';

$action = $_GET['action'] ?? $_POST['action'] ?? 'list';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'create') {
    $appName = trim($_POST['app_name'] ?? 'ScalePixel Integration');
    $plan = trim($_POST['plan'] ?? 'Developer Free');

    $newKey = ScalePixelAuth::generateKeyPair($appName, $plan);

    echo json_encode([
        'status'  => 'success',
        'message' => 'New API Key pair generated successfully.',
        'data'    => $newKey,
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    exit;
}

// List all keys
$allKeys = ScalePixelAuth::getKeys();
$formatted = [];

foreach ($allKeys as $pk => $info) {
    // Mask secret key for security (never expose full private secret in public list)
    $maskedSecret = 'sp_live_sk_...' . substr($info['secret'], -4);
    $formatted[] = [
        'source_key'  => $pk,
        'secret_key_masked' => $maskedSecret,
        'name'        => $info['name'],
        'plan'        => $info['plan'],
        'total_calls' => $info['total_calls'],
        'bytes_saved' => $info['bytes_saved'],
        'mb_saved'    => round(($info['bytes_saved'] ?? 0) / (1024 * 1024), 2),
        'created_at'  => $info['created_at'],
        'last_used'   => $info['last_used'],
        'active'      => $info['active'],
    ];
}

echo json_encode([
    'status' => 'success',
    'total'  => count($formatted),
    'keys'   => $formatted,
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
exit;
