<?php
/**
 * ScalePixel PHP Client SDK - Drop-in integration for Web & PHP Backend Applications
 */

class ScalePixelClient {
    private $apiUrl;
    private $sourceKey;
    private $secretKey;

    public function __construct($apiUrl = null, $sourceKey = null, $secretKey = null) {
        $this->apiUrl = rtrim($apiUrl ?? 'http://localhost/ScalePixel/api/v1', '/');
        $this->sourceKey = $sourceKey ?? 'sp_live_pk_scalepixel_pro';
        $this->secretKey = $secretKey ?? 'sp_live_sk_e37104bfa9c820173491823abce04192';
    }

    /**
     * Compress a Provider Profile Photo / Avatar (JPEG, 600x600 @ 80%)
     * Result format: provider_{id}_{YYYY-MM-DD_HH-mm}.jpg
     */
    public function compressAvatar($filePath, $providerId, $includeSeconds = true) {
        return $this->sendRequest('/compress.php', [
            'file'            => new CURLFile($filePath),
            'mode'            => 'avatar',
            'prefix'          => 'provider_' . intval($providerId),
            'include_seconds' => $includeSeconds ? '1' : '0',
            'format'          => 'jpeg',
            'quality'         => '80',
            'width'           => '600',
            'height'          => '600',
            'fit'             => 'cover',
        ]);
    }

    /**
     * Compress Shop / Garage / Workshop Showcase Photo (WebP, 1080px max @ 75%)
     */
    public function compressShowcase($filePath, $providerId) {
        return $this->sendRequest('/compress.php', [
            'file'            => new CURLFile($filePath),
            'mode'            => 'showcase',
            'prefix'          => 'shop_' . intval($providerId),
            'include_seconds' => '1',
            'format'          => 'webp',
            'quality'         => '75',
            'width'           => '1080',
            'fit'             => 'contain',
        ]);
    }

    /**
     * Universal Format Converter
     */
    public function convert($filePath, $targetFormat = 'webp', $quality = 85) {
        return $this->sendRequest('/convert.php', [
            'file'    => new CURLFile($filePath),
            'to'      => $targetFormat,
            'quality' => strval($quality),
        ]);
    }

    /**
     * Internal cURL executor
     */
    private function sendRequest($endpoint, $postFields) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->apiUrl . $endpoint);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'X-ScalePixel-Key: ' . $this->sourceKey,
            'X-ScalePixel-Secret: ' . $this->secretKey,
            'Authorization: Bearer ' . $this->secretKey,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            return ['status' => 'error', 'message' => "ScalePixel cURL Error: {$error}"];
        }

        $decoded = json_decode($response, true);
        if (!$decoded) {
            return ['status' => 'error', 'message' => "Invalid response from ScalePixel (HTTP {$httpCode})", 'raw' => $response];
        }

        return $decoded;
    }
}
