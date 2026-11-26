<?php
/**
 * ScalePixel - API Authentication, Key Management & Quota Tracking
 */

require_once __DIR__ . '/Security.php';

class ScalePixelAuth {

    private static $keysFile = __DIR__ . '/../storage/keys.json';

    /**
     * Load all registered API keys
     */
    public static function getKeys() {
        if (!file_exists(self::$keysFile)) {
            self::initDefaultKeys();
        }
        $raw = @file_get_contents(self::$keysFile);
        $data = json_decode($raw, true);
        if (!is_array($data)) {
            $data = self::initDefaultKeys();
        }
        return $data;
    }

    /**
     * Initialize default production & demo keys
     */
    private static function initDefaultKeys() {
        $dir = dirname(self::$keysFile);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $defaultData = [
            'sp_live_pk_scalepixel_starter' => [
                'name'         => 'ScalePixel Starter Engine',
                'secret'       => 'sp_live_sk_a92d8f1e4c7b30129481bfe48102a991',
                'plan'         => 'Developer Starter (₹9 / month)',
                'created_at'   => '2026-11-26 12:00:00',
                'active'       => true,
                'rate_limit'   => 10000,
                'total_calls'  => 0,
                'bytes_saved'  => 0,
                'last_used'    => null,
            ],
            'sp_live_pk_scalepixel_pro' => [
                'name'         => 'ScalePixel Pro Production',
                'secret'       => 'sp_live_sk_e37104bfa9c820173491823abce04192',
                'plan'         => 'Pro Enterprise (₹49 / month)',
                'created_at'   => '2026-11-26 12:00:00',
                'active'       => true,
                'rate_limit'   => 100000,
                'total_calls'  => 0,
                'bytes_saved'  => 0,
                'last_used'    => null,
            ],
            'sp_test_pk_developer_sandbox' => [
                'name'         => 'ScalePixel Sandbox & Testing',
                'secret'       => 'sp_test_sk_developer_sandbox_secret',
                'plan'         => 'Developer Sandbox',
                'created_at'   => '2026-11-26 12:00:00',
                'active'       => true,
                'rate_limit'   => 50000,
                'total_calls'  => 0,
                'bytes_saved'  => 0,
                'last_used'    => null,
            ]
        ];

        file_put_contents(self::$keysFile, json_encode($defaultData, JSON_PRETTY_PRINT));
        return $defaultData;
    }

    /**
     * Generate a new key pair
     */
    public static function generateKeyPair($appName = 'My App', $plan = 'Developer Free') {
        $keys = self::getKeys();

        $sourceKey = 'sp_live_pk_' . bin2hex(random_bytes(10));
        $secretKey = 'sp_live_sk_' . bin2hex(random_bytes(16));

        $keys[$sourceKey] = [
            'name'         => htmlspecialchars(trim($appName)),
            'secret'       => $secretKey,
            'plan'         => $plan,
            'created_at'   => date('Y-m-d H:i:s'),
            'active'       => true,
            'rate_limit'   => 100000,
            'total_calls'  => 0,
            'bytes_saved'  => 0,
            'last_used'    => null,
        ];

        file_put_contents(self::$keysFile, json_encode($keys, JSON_PRETTY_PRINT));

        return [
            'source_key' => $sourceKey,
            'secret_key' => $secretKey,
            'name'       => $appName,
            'plan'       => $plan,
            'created_at' => $keys[$sourceKey]['created_at'],
        ];
    }

    /**
     * Authenticate an incoming API request
     * Checks headers, bearer token, and query params
     */
    public static function authenticate($allowWebToolSession = true) {
        $headers = self::getRequestHeaders();

        $sourceKey = $headers['x-scalepixel-key'] 
            ?? $headers['x-api-key'] 
            ?? $_POST['api_key'] 
            ?? $_GET['api_key'] 
            ?? null;

        $secretKey = $headers['x-scalepixel-secret'] 
            ?? $headers['x-api-secret'] 
            ?? $_POST['api_secret'] 
            ?? $_GET['api_secret'] 
            ?? null;

        // Check Bearer Token in Authorization header
        if (!$secretKey && !empty($headers['authorization'])) {
            if (preg_match('/Bearer\s+(\S+)/i', $headers['authorization'], $matches)) {
                $secretKey = $matches[1];
            }
        }

        // Allow web tool session (drag & drop on the website itself)
        if (empty($sourceKey) && empty($secretKey) && $allowWebToolSession) {
            $referer = $_SERVER['HTTP_REFERER'] ?? '';
            $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
            $host = $_SERVER['HTTP_HOST'] ?? '';

            if ((!empty($referer) && strpos($referer, $host) !== false) || (!empty($origin) && strpos($origin, $host) !== false)) {
                return [
                    'authenticated' => true,
                    'is_web_client' => true,
                    'key_data'      => [
                        'name' => 'ScalePixel Web Studio',
                        'plan' => 'Web Public',
                    ],
                    'source_key'    => 'web_studio_session',
                ];
            }
        }

        if (empty($sourceKey) && empty($secretKey)) {
            return [
                'authenticated' => false,
                'error'         => 'Missing API credentials. Provide X-ScalePixel-Key / X-ScalePixel-Secret headers or Bearer token.',
            ];
        }

        $allKeys = self::getKeys();

        // Match by sourceKey first
        if ($sourceKey && isset($allKeys[$sourceKey])) {
            $keyInfo = $allKeys[$sourceKey];
            if (!$keyInfo['active']) {
                return ['authenticated' => false, 'error' => 'This API key has been suspended or deactivated.'];
            }
            if ($secretKey && !ScalePixelSecurity::timingSafeEquals($keyInfo['secret'], $secretKey)) {
                return ['authenticated' => false, 'error' => 'Invalid API Secret Key provided for this Source Key.'];
            }

            self::recordUsage($sourceKey);
            return [
                'authenticated' => true,
                'is_web_client' => false,
                'key_data'      => $keyInfo,
                'source_key'    => $sourceKey,
            ];
        }

        // Match by secretKey directly (Bearer Token mode)
        if ($secretKey) {
            foreach ($allKeys as $pk => $info) {
                if (ScalePixelSecurity::timingSafeEquals($info['secret'], $secretKey)) {
                    if (!$info['active']) {
                        return ['authenticated' => false, 'error' => 'This API key has been suspended.'];
                    }
                    self::recordUsage($pk);
                    return [
                        'authenticated' => true,
                        'is_web_client' => false,
                        'key_data'      => $info,
                        'source_key'    => $pk,
                    ];
                }
            }
        }

        return [
            'authenticated' => false,
            'error'         => 'Invalid API credentials. Please verify your ScalePixel Source Key and Secret Key.',
        ];
    }

    /**
     * Record usage statistics
     */
    public static function recordUsage($sourceKey, $bytesSaved = 0) {
        $keys = self::getKeys();
        if (isset($keys[$sourceKey])) {
            $keys[$sourceKey]['total_calls'] = ($keys[$sourceKey]['total_calls'] ?? 0) + 1;
            if ($bytesSaved > 0) {
                $keys[$sourceKey]['bytes_saved'] = ($keys[$sourceKey]['bytes_saved'] ?? 0) + $bytesSaved;
            }
            $keys[$sourceKey]['last_used'] = date('Y-m-d H:i:s');
            @file_put_contents(self::$keysFile, json_encode($keys, JSON_PRETTY_PRINT));
        }
    }

    /**
     * Normalize incoming request headers
     */
    private static function getRequestHeaders() {
        $headers = [];
        foreach ($_SERVER as $key => $value) {
            if (substr($key, 0, 5) === 'HTTP_') {
                $header = strtolower(str_replace('_', '-', substr($key, 5)));
                $headers[$header] = $value;
            }
        }
        return $headers;
    }
}
