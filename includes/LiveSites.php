<?php
/**
 * ScalePixel - Ephemeral URL-Only Website Monitor & Admin Auth (No Database)
 * Stores ONLY normalized client website URLs in storage/live_sites.json.
 */

class ScalePixelLiveSites {

    private static $logFile = __DIR__ . '/../storage/live_sites.json';
    private static $adminFile = __DIR__ . '/../storage/admin.json';

    /**
     * Start a hardened PHP session for admin authentication.
     */
    public static function startSecureSession(): void {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }
        $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https')
            || (isset($_SERVER['SERVER_PORT']) && (int)$_SERVER['SERVER_PORT'] === 443);

        session_set_cookie_params([
            'lifetime' => 0,
            'path'     => '/',
            'secure'   => $isHttps,
            'httponly' => true,
            'samesite' => 'Strict',
        ]);
        session_start();

        if (empty($_SESSION['sp_admin_csrf'])) {
            $_SESSION['sp_admin_csrf'] = bin2hex(random_bytes(24));
        }
    }

    /**
     * Check whether an admin password has been configured via env or local file.
     */
    public static function isAdminConfigured(): bool {
        $envHash = getenv('SCALEPIXEL_ADMIN_HASH');
        if (is_string($envHash) && $envHash !== '') {
            return true;
        }
        if (!file_exists(self::$adminFile)) {
            return false;
        }
        $data = json_decode((string)@file_get_contents(self::$adminFile), true);
        return is_array($data) && !empty($data['password_hash']);
    }

    /**
     * Set initial admin password when none is configured yet.
     */
    public static function setupInitialAdmin(string $password): array {
        if (self::isAdminConfigured()) {
            return ['ok' => false, 'error' => 'Admin password is already configured.'];
        }
        if (strlen($password) < 8) {
            return ['ok' => false, 'error' => 'Password must be at least 8 characters long.'];
        }

        $dir = dirname(self::$adminFile);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $payload = json_encode([
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
            'updated_at'    => date('c'),
        ], JSON_PRETTY_PRINT);

        $fp = @fopen(self::$adminFile, 'c+');
        if (!$fp) {
            return ['ok' => false, 'error' => 'Unable to write admin configuration file.'];
        }
        flock($fp, LOCK_EX);
        ftruncate($fp, 0);
        rewind($fp);
        fwrite($fp, $payload);
        fflush($fp);
        flock($fp, LOCK_UN);
        fclose($fp);
        @chmod(self::$adminFile, 0600);

        return ['ok' => true];
    }

    /**
     * Verify admin password with brute-force lockout protection.
     */
    public static function attemptLogin(string $password): array {
        self::startSecureSession();

        $lockUntil = (int)($_SESSION['sp_admin_lock_until'] ?? 0);
        if ($lockUntil > time()) {
            $wait = $lockUntil - time();
            return ['ok' => false, 'error' => "Too many failed attempts. Try again in {$wait} seconds."];
        }

        $hash = getenv('SCALEPIXEL_ADMIN_HASH');
        if (!is_string($hash) || $hash === '') {
            $data = json_decode((string)@file_get_contents(self::$adminFile), true);
            $hash = is_array($data) ? ($data['password_hash'] ?? '') : '';
        }

        if ($hash !== '' && password_verify($password, $hash)) {
            session_regenerate_id(true);
            $_SESSION['sp_admin_auth'] = true;
            $_SESSION['sp_admin_fails'] = 0;
            unset($_SESSION['sp_admin_lock_until']);
            return ['ok' => true];
        }

        $fails = (int)($_SESSION['sp_admin_fails'] ?? 0) + 1;
        $_SESSION['sp_admin_fails'] = $fails;
        if ($fails >= 5) {
            $_SESSION['sp_admin_lock_until'] = time() + 300;
            $_SESSION['sp_admin_fails'] = 0;
            return ['ok' => false, 'error' => 'Too many failed attempts. Locked for 5 minutes.'];
        }

        return ['ok' => false, 'error' => 'Invalid admin password.'];
    }

    public static function isAuthenticated(): bool {
        self::startSecureSession();
        return !empty($_SESSION['sp_admin_auth']) && $_SESSION['sp_admin_auth'] === true;
    }

    public static function logout(): void {
        self::startSecureSession();
        $_SESSION['sp_admin_auth'] = false;
        unset($_SESSION['sp_admin_auth']);
        session_regenerate_id(true);
    }

    public static function verifyCsrf(?string $token): bool {
        self::startSecureSession();
        return is_string($token)
            && !empty($_SESSION['sp_admin_csrf'])
            && hash_equals($_SESSION['sp_admin_csrf'], $token);
    }

    /**
     * Extract and normalize ONLY the base website URL (scheme://host[:port]) from request headers.
     */
    public static function extractSiteUrl(): ?string {
        $candidates = [
            $_SERVER['HTTP_ORIGIN'] ?? '',
            $_SERVER['HTTP_REFERER'] ?? '',
            $_SERVER['HTTP_X_SITE_URL'] ?? '',
            $_POST['site_url'] ?? '',
        ];

        foreach ($candidates as $raw) {
            $raw = trim((string)$raw);
            if ($raw === '') {
                continue;
            }
            $parts = @parse_url($raw);
            if (!is_array($parts) || empty($parts['scheme']) || empty($parts['host'])) {
                continue;
            }
            $scheme = strtolower($parts['scheme']);
            if ($scheme !== 'http' && $scheme !== 'https') {
                continue;
            }
            $host = strtolower($parts['host']);
            $isValidHost = filter_var($host, FILTER_VALIDATE_IP) !== false
                || filter_var($host, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME) !== false;
            if (!$isValidHost) {
                continue;
            }

            $normalized = $scheme . '://' . $host;
            if (!empty($parts['port'])) {
                $port = (int)$parts['port'];
                if ($port > 0 && $port <= 65535) {
                    $normalized .= ':' . $port;
                }
            }
            return $normalized;
        }

        return null;
    }

    /**
     * Record ONLY the website URL into storage/live_sites.json (no IPs, keys, or metadata).
     */
    public static function recordFromRequest(bool $isWebStudioSession = false): void {
        $siteUrl = self::extractSiteUrl();
        if ($siteUrl === null) {
            return;
        }

        if ($isWebStudioSession) {
            $ownHost = strtolower((string)($_SERVER['HTTP_HOST'] ?? ''));
            $siteHost = strtolower((string)parse_url($siteUrl, PHP_URL_HOST));
            $sitePort = parse_url($siteUrl, PHP_URL_PORT);
            $siteHostWithPort = $sitePort ? ($siteHost . ':' . $sitePort) : $siteHost;
            if ($ownHost !== '' && ($siteHost === $ownHost || $siteHostWithPort === $ownHost)) {
                return;
            }
        }

        $dir = dirname(self::$logFile);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $fp = @fopen(self::$logFile, 'c+');
        if (!$fp) {
            return;
        }
        if (flock($fp, LOCK_EX)) {
            $raw = stream_get_contents($fp);
            $sites = json_decode((string)$raw, true);
            if (!is_array($sites)) {
                $sites = [];
            }

            if (!in_array($siteUrl, $sites, true)) {
                $sites[] = $siteUrl;
                // Keep log bounded to prevent unbounded growth
                if (count($sites) > 500) {
                    $sites = array_slice($sites, -500);
                }
                ftruncate($fp, 0);
                rewind($fp);
                fwrite($fp, json_encode(array_values($sites), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
                fflush($fp);
            }
            flock($fp, LOCK_UN);
        }
        fclose($fp);
    }

    /**
     * Read logged website URLs.
     */
    public static function getSites(): array {
        if (!file_exists(self::$logFile)) {
            return [];
        }
        $fp = @fopen(self::$logFile, 'r');
        if (!$fp) {
            return [];
        }
        $sites = [];
        if (flock($fp, LOCK_SH)) {
            $raw = stream_get_contents($fp);
            $decoded = json_decode((string)$raw, true);
            if (is_array($decoded)) {
                foreach ($decoded as $url) {
                    if (is_string($url) && preg_match('#^https?://#i', $url)) {
                        $sites[] = $url;
                    }
                }
            }
            flock($fp, LOCK_UN);
        }
        fclose($fp);
        return array_values(array_unique($sites));
    }

    /**
     * Clear all logged website URLs immediately.
     */
    public static function clear(): void {
        $dir = dirname(self::$logFile);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        $fp = @fopen(self::$logFile, 'c+');
        if (!$fp) {
            return;
        }
        if (flock($fp, LOCK_EX)) {
            ftruncate($fp, 0);
            rewind($fp);
            fwrite($fp, '[]');
            fflush($fp);
            flock($fp, LOCK_UN);
        }
        fclose($fp);
    }
}
