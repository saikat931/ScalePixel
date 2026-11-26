<?php
/**
 * ScalePixel - CLI Background Worker
 * Handles image processing via PHP CLI when GD is not loaded in web server module
 */

if (php_sapi_name() !== 'cli') {
    http_response_code(403);
    die('Forbidden: CLI only.');
}

require_once __DIR__ . '/ImageProcessor.php';

$payloadFile = $argv[1] ?? null;

if (!$payloadFile || !file_exists($payloadFile)) {
    echo json_encode(['status' => 'error', 'message' => 'Missing or invalid payload file']);
    exit(1);
}

$payload = json_decode(file_get_contents($payloadFile), true);
if (!$payload) {
    echo json_encode(['status' => 'error', 'message' => 'Malformed JSON payload']);
    exit(1);
}

try {
    $result = ImageProcessor::processDirect(
        $payload['source_path'],
        $payload['destination_path'],
        $payload['options'] ?? []
    );
    echo json_encode(['status' => 'success', 'data' => $result]);
} catch (Throwable $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    exit(1);
}

