<?php
$pageTitle = 'ScalePixel - REST API Documentation & Integration Guide';
$activeNav = 'docs';
require_once __DIR__ . '/includes/header.php';
?>

<div class="page-container" style="max-width: 1040px;">
    <div class="hero-pill" style="margin-bottom: 8px;">Developer Documentation</div>
    <h1 style="font-size: 36px; font-weight: 900; letter-spacing: -0.5px; margin-bottom: 12px;">ScalePixel REST API Reference</h1>
    <p style="color: var(--text-secondary); font-size: 16px; margin-bottom: 40px;">
        Integrate automated, sub-80ms image compression and universal format conversion into your mobile applications, backend microservices, or frontend forms.
    </p>

    <!-- Navigation jump chips -->
    <div style="display: flex; gap: 10px; margin-bottom: 36px; flex-wrap: wrap;">
        <a href="#auth" class="format-pill" style="text-decoration: none; padding: 6px 14px; font-size: 13px;">1. Authentication</a>
        <a href="#compress" class="format-pill" style="text-decoration: none; padding: 6px 14px; font-size: 13px;">2. /compress API</a>
        <a href="#convert" class="format-pill" style="text-decoration: none; padding: 6px 14px; font-size: 13px;">3. /convert API</a>
        <a href="#avatar-mode" class="format-pill" style="text-decoration: none; padding: 6px 14px; font-size: 13px; color: var(--emerald); border-color: rgba(0,245,160,0.3);">4. Avatar Mode & Naming</a>
        <a href="#scalepixel-sdk" class="format-pill" style="text-decoration: none; padding: 6px 14px; font-size: 13px; color: var(--cyan); border-color: rgba(0,242,254,0.3);">5. ScalePixel SDKs</a>
    </div>

    <!-- 1. Authentication -->
    <div class="glass-card" id="auth" style="margin-bottom: 36px;">
        <h2 style="color: var(--cyan); font-size: 20px; font-weight: 800; margin-bottom: 12px;">1. API Authentication</h2>
        <p style="color: var(--text-secondary); font-size: 14px; margin-bottom: 16px;">
            Every request must include your ScalePixel credentials. You can pass them using custom HTTP headers, Bearer tokens, or form body parameters.
        </p>

        <h4 style="font-size: 13px; color: #FFF; text-transform: uppercase; margin-bottom: 8px;">Method A: HTTP Headers (Recommended)</h4>
        <div class="code-block">
X-ScalePixel-Key: sp_live_pk_YOUR_API_KEY
X-ScalePixel-Secret: sp_live_sk_YOUR_SECRET_KEY
        </div>

        <h4 style="font-size: 13px; color: #FFF; text-transform: uppercase; margin-bottom: 8px;">Method B: Bearer Token</h4>
        <div class="code-block">
Authorization: Bearer sp_live_sk_YOUR_SECRET_KEY
        </div>
    </div>

    <!-- 2. POST /compress -->
    <div class="glass-card" id="compress" style="margin-bottom: 36px;">
        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 12px;">
            <span style="background: rgba(0,242,254,0.2); color: var(--cyan); padding: 4px 10px; border-radius: 6px; font-weight: 800; font-size: 13px;">POST</span>
            <code style="font-size: 16px; color: #FFF; font-weight: 700;">/api/v1/compress.php</code>
        </div>
        <p style="color: var(--text-secondary); font-size: 14px; margin-bottom: 20px;">
            Compresses, resizes, optimizes quality, and outputs a lightweight public URL for any image.
        </p>

        <h4 style="font-size: 13px; color: #FFF; text-transform: uppercase; margin-bottom: 8px;">Request Parameters (multipart/form-data)</h4>
        <table style="width: 100%; border-collapse: collapse; font-size: 13px; margin-bottom: 20px;">
            <thead>
                <tr style="border-bottom: 1px solid var(--border-subtle); color: var(--text-muted); text-align: left;">
                    <th style="padding: 8px 12px;">PARAMETER</th>
                    <th style="padding: 8px 12px;">TYPE</th>
                    <th style="padding: 8px 12px;">DEFAULT</th>
                    <th style="padding: 8px 12px;">DESCRIPTION</th>
                </tr>
            </thead>
            <tbody style="color: var(--text-secondary);">
                <tr style="border-bottom: 1px solid rgba(255,255,255,0.04);">
                    <td style="padding: 10px 12px; color: var(--cyan); font-weight: 700;">file</td>
                    <td style="padding: 10px 12px;">Binary File</td>
                    <td style="padding: 10px 12px;">Required</td>
                    <td style="padding: 10px 12px;">The raw image file to compress (JPEG, PNG, WebP, AVIF, GIF, BMP).</td>
                </tr>
                <tr style="border-bottom: 1px solid rgba(255,255,255,0.04);">
                    <td style="padding: 10px 12px; color: var(--cyan); font-weight: 700;">mode</td>
                    <td style="padding: 10px 12px;">string</td>
                    <td style="padding: 10px 12px;">balanced</td>
                    <td style="padding: 10px 12px;"><code>avatar</code> (600x600 JPEG), <code>showcase</code> (1080px WebP), <code>lossless</code>, <code>max</code>.</td>
                </tr>
                <tr style="border-bottom: 1px solid rgba(255,255,255,0.04);">
                    <td style="padding: 10px 12px; color: var(--cyan); font-weight: 700;">prefix</td>
                    <td style="padding: 10px 12px;">string</td>
                    <td style="padding: 10px 12px;">media</td>
                    <td style="padding: 10px 12px;">Filename prefix. E.g. <code>provider_19</code> generates <code>provider_19_2026-11-26_12-30.jpg</code>.</td>
                </tr>
                <tr style="border-bottom: 1px solid rgba(255,255,255,0.04);">
                    <td style="padding: 10px 12px; color: var(--cyan); font-weight: 700;">quality</td>
                    <td style="padding: 10px 12px;">integer</td>
                    <td style="padding: 10px 12px;">80</td>
                    <td style="padding: 10px 12px;">Compression quality from 1 to 100.</td>
                </tr>
                <tr>
                    <td style="padding: 10px 12px; color: var(--cyan); font-weight: 700;">format</td>
                    <td style="padding: 10px 12px;">string</td>
                    <td style="padding: 10px 12px;">auto</td>
                    <td style="padding: 10px 12px;">Target output format: <code>jpeg</code>, <code>webp</code>, <code>avif</code>, <code>png</code>.</td>
                </tr>
            </tbody>
        </table>

        <h4 style="font-size: 13px; color: #FFF; text-transform: uppercase; margin-bottom: 8px;">Sample JSON Response</h4>
        <div class="code-block">
{
  "status": "success",
  "message": "Image compressed and optimized successfully.",
  "data": {
    "filename": "provider_19_2026-11-26_12-30-45.jpg",
    "download_url": "http://localhost/ScalePixel/storage/temp/provider_19_2026-11-26_12-30-45.jpg",
    "original_size_kb": 8450.25,
    "compressed_size_kb": 74.82,
    "saved_bytes": 8576440,
    "saved_percent": 99.12,
    "original_dimensions": "4032x3024",
    "new_dimensions": "600x600",
    "format": "jpeg",
    "mode": "avatar",
    "execution_time_ms": 68.4
  }
}
        </div>
    </div>

    <!-- 3. Avatar Mode Highlight -->
    <div class="glass-card" id="avatar-mode" style="border-color: rgba(0, 245, 160, 0.4); margin-bottom: 36px;">
        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 8px;">
            <span style="font-size: 22px;">👤</span>
            <h2 style="color: var(--emerald); font-size: 20px; font-weight: 800;">Human Face / Provider Avatar Mode</h2>
        </div>
        <p style="color: var(--text-secondary); font-size: 14px; margin-bottom: 16px;">
            By setting <code>mode: 'avatar'</code>, ScalePixel automatically enforces the exact specifications chosen for your service provider profile avatars:
        </p>
        <ul style="color: var(--text-secondary); font-size: 14px; padding-left: 20px; line-height: 1.8;">
            <li><strong>Auto Aspect-Fill & Crop</strong>: Converts any wide landscape or tall portrait into a clean 1:1 square (600×600 px).</li>
            <li><strong>Optimal Face JPEG Compression</strong>: Compresses with high-fidelity 80% JPEG, keeping skin tones sharp and natural while dropping file size to ~75 KB.</li>
            <li><strong>Exact Timestamped Filename</strong>: Uses your chosen format <code>prefix_YYYY-MM-DD_HH-mm-ss.jpg</code> (e.g. <code>provider_19_2026-11-26_12-30-45.jpg</code>) for 100% cache-busting.</li>
        </ul>
    </div>

    <!-- 4. ScalePixel SDK Integration -->
    <div class="glass-card" id="scalepixel-sdk" style="margin-bottom: 36px;">
        <h2 style="color: var(--cyan); font-size: 20px; font-weight: 800; margin-bottom: 12px;">4. ScalePixel Client Integration SDKs</h2>
        <p style="color: var(--text-secondary); font-size: 14px; margin-bottom: 16px;">
            Drop-in client libraries are pre-configured inside the <code>integrations/</code> folder for your apps and services:
        </p>

        <h4 style="font-size: 13px; color: #FFF; text-transform: uppercase; margin-bottom: 8px;">React Native / Mobile App (TypeScript):</h4>
        <div class="code-block">
import { ScalePixelClient } from './integrations/ScalePixelClient';

const scalePixel = new ScalePixelClient('http://localhost/ScalePixel/api/v1');

// Upload & compress Provider Avatar in 1 line:
const result = await scalePixel.compressAvatar(selectedPhotoUri, providerId);
console.log('New Avatar URL:', result.data.download_url);
console.log('Saved:', result.data.saved_percent + '%');
        </div>

        <h4 style="font-size: 13px; color: #FFF; text-transform: uppercase; margin-bottom: 8px; margin-top: 16px;">PHP Backend / Web Server:</h4>
        <div class="code-block">
require_once 'integrations/ScalePixelClient.php';

$client = new ScalePixelClient();
$result = $client->compressAvatar('/tmp/uploaded_photo.jpg', 19);

if ($result['status'] === 'success') {
    $avatarUrl = $result['data']['download_url'];
    // Save $avatarUrl in MySQL providers table
}
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
