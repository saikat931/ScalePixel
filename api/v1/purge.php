<?php
/**
 * ScalePixel - Auto-Purge Ephemeral Files
 * Automatically cleans up temporary files older than 1 hour to ensure 0 persistent data leaks.
 */

header('Content-Type: application/json');

$dir = __DIR__ . '/../../storage/temp/';
$maxAgeSeconds = 3600; // 1 Hour
$deletedCount = 0;
$freedBytes = 0;

if (is_dir($dir)) {
    $files = scandir($dir);
    $now = time();

    foreach ($files as $file) {
        if ($file === '.' || $file === '..' || $file === '.gitkeep' || $file === '.htaccess') {
            continue;
        }

        $filePath = $dir . $file;
        if (is_file($filePath)) {
            $fileAge = $now - filemtime($filePath);
            if ($fileAge > $maxAgeSeconds) {
                $freedBytes += filesize($filePath);
                if (@unlink($filePath)) {
                    $deletedCount++;
                }
            }
        }
    }
}

echo json_encode([
    'status'        => 'success',
    'deleted_files' => $deletedCount,
    'freed_kb'      => round($freedBytes / 1024, 2),
    'timestamp'     => date('Y-m-d H:i:s'),
]);
exit;
