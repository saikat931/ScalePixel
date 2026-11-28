<?php
require_once __DIR__ . '/includes/icons.php';

$pageTitle = 'ScalePixel - REST API Documentation & Integration Guide';
$activeNav = 'docs';
$pageEyebrow = 'Developer Documentation';
$pageHeading = 'REST API reference';
$pageSubtitle = 'Integrate automated, sub-80ms image compression and universal format conversion into mobile apps, microservices, and web forms.';
$headerActionsHtml = '<a href="api-dashboard.php" class="sp-btn sp-btn-primary">' . sp_icon('key', '', 16) . '<span>Manage API Keys</span></a>';

require_once __DIR__ . '/includes/header.php';
?>

<!-- Navigation jump chips -->
<nav class="sp-pill-row" style="justify-content: flex-start;" aria-label="Documentation sections">
    <a href="#auth" class="sp-pill sp-pill-accent">1. Authentication</a>
    <a href="#compress" class="sp-pill">2. /compress API</a>
    <a href="#convert" class="sp-pill">3. /convert API</a>
    <a href="#avatar-mode" class="sp-pill">4. Avatar Mode &amp; Naming</a>
    <a href="#scalepixel-sdk" class="sp-pill">5. ScalePixel SDKs</a>
</nav>

<!-- 1. Authentication -->
<section class="sp-glass" id="auth">
    <div class="sp-card-header">
        <h2 class="sp-card-title" style="font-size: 20px;">
            <?= sp_icon('lock', '', 18) ?>
            <span>1. API Authentication</span>
        </h2>
    </div>
    <p style="color: var(--sp-text-secondary); margin-bottom: 16px;">
        Every API request must include your ScalePixel credentials via HTTP headers or Bearer token authorization.
    </p>

    <h4>Method A: HTTP Headers (Recommended)</h4>
    <div class="sp-code-wrap">
        <pre class="sp-code" id="code-auth-headers">X-ScalePixel-Key: sp_live_pk_YOUR_API_KEY
X-ScalePixel-Secret: sp_live_sk_YOUR_SECRET_KEY</pre>
        <button type="button" class="sp-btn sp-btn-sm" data-copy-target="code-auth-headers" style="margin-top: 8px;">
            <?= sp_icon('copy', '', 14) ?>
            <span>Copy Headers</span>
        </button>
    </div>

    <h4 style="margin-top: 16px;">Method B: Bearer Token</h4>
    <div class="sp-code-wrap">
        <pre class="sp-code" id="code-auth-bearer">Authorization: Bearer sp_live_sk_YOUR_SECRET_KEY</pre>
        <button type="button" class="sp-btn sp-btn-sm" data-copy-target="code-auth-bearer" style="margin-top: 8px;">
            <?= sp_icon('copy', '', 14) ?>
            <span>Copy Bearer Header</span>
        </button>
    </div>
</section>

<!-- 2. POST /compress -->
<section class="sp-glass" id="compress">
    <div class="sp-card-header">
        <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
            <span class="sp-badge sp-badge-accent">POST</span>
            <h2 style="font-size: 20px;"><code>/api/v1/compress.php</code></h2>
        </div>
    </div>
    <p style="color: var(--sp-text-secondary); margin-bottom: 16px;">
        Compresses, resizes, optimizes quality, and returns a lightweight download URL for any uploaded image.
    </p>

    <h4 style="margin-bottom: 10px;">Request Parameters (multipart/form-data)</h4>
    <div class="sp-table-wrap" style="margin-bottom: 20px;">
        <table class="sp-table">
            <thead>
                <tr>
                    <th>PARAMETER</th>
                    <th>TYPE</th>
                    <th>DEFAULT</th>
                    <th>DESCRIPTION</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="color: var(--sp-accent); font-weight: 600;">file</td>
                    <td>Binary File</td>
                    <td>Required</td>
                    <td>Raw image file to compress (JPEG, PNG, WebP, AVIF, GIF, BMP).</td>
                </tr>
                <tr>
                    <td style="color: var(--sp-accent); font-weight: 600;">mode</td>
                    <td>string</td>
                    <td>balanced</td>
                    <td><code>avatar</code> (600&times;600 JPEG), <code>showcase</code> (1920px WebP), <code>document</code>, <code>lossless</code>, <code>max</code>.</td>
                </tr>
                <tr>
                    <td style="color: var(--sp-accent); font-weight: 600;">prefix</td>
                    <td>string</td>
                    <td>media</td>
                    <td>Filename prefix. Example: <code>provider_19</code> generates <code>provider_19_2026-11-26_12-30-45.jpg</code>.</td>
                </tr>
                <tr>
                    <td style="color: var(--sp-accent); font-weight: 600;">quality</td>
                    <td>integer</td>
                    <td>80</td>
                    <td>Compression quality from 1 to 100.</td>
                </tr>
                <tr>
                    <td style="color: var(--sp-accent); font-weight: 600;">format</td>
                    <td>string</td>
                    <td>auto</td>
                    <td>Target output format: <code>jpeg</code>, <code>webp</code>, <code>avif</code>, <code>png</code>, <code>ico</code>.</td>
                </tr>
            </tbody>
        </table>
    </div>

    <h4>Sample JSON Response</h4>
    <div class="sp-code-wrap">
        <pre class="sp-code" id="code-compress-json">{
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
}</pre>
        <button type="button" class="sp-btn sp-btn-sm" data-copy-target="code-compress-json" style="margin-top: 8px;">
            <?= sp_icon('copy', '', 14) ?>
            <span>Copy JSON</span>
        </button>
    </div>
</section>

<!-- 3. POST /convert -->
<section class="sp-glass" id="convert">
    <div class="sp-card-header">
        <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
            <span class="sp-badge sp-badge-accent">POST</span>
            <h2 style="font-size: 20px;"><code>/api/v1/convert.php</code></h2>
        </div>
    </div>
    <p style="color: var(--sp-text-secondary); margin-bottom: 16px;">
        Universal any-to-any image format converter across JPG, PNG, WebP, AVIF, GIF, BMP, and ICO.
    </p>

    <div class="sp-table-wrap">
        <table class="sp-table">
            <thead>
                <tr>
                    <th>PARAMETER</th>
                    <th>TYPE</th>
                    <th>DEFAULT</th>
                    <th>DESCRIPTION</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="color: var(--sp-accent); font-weight: 600;">file</td>
                    <td>Binary File</td>
                    <td>Required</td>
                    <td>Source image uploaded via multipart/form-data.</td>
                </tr>
                <tr>
                    <td style="color: var(--sp-accent); font-weight: 600;">to</td>
                    <td>string</td>
                    <td>webp</td>
                    <td>Target format: <code>jpeg</code>, <code>png</code>, <code>webp</code>, <code>avif</code>, <code>gif</code>, <code>bmp</code>, <code>ico</code>.</td>
                </tr>
                <tr>
                    <td style="color: var(--sp-accent); font-weight: 600;">quality</td>
                    <td>integer</td>
                    <td>85</td>
                    <td>Output encoder quality from 1 to 100.</td>
                </tr>
                <tr>
                    <td style="color: var(--sp-accent); font-weight: 600;">prefix</td>
                    <td>string</td>
                    <td>source name</td>
                    <td>Optional filename prefix for output naming.</td>
                </tr>
            </tbody>
        </table>
    </div>
</section>

<!-- 4. Avatar Mode Highlight -->
<section class="sp-glass" id="avatar-mode" style="border-color: rgba(168, 197, 139, 0.32);">
    <div class="sp-card-header">
        <h2 class="sp-card-title" style="font-size: 20px;">
            <?= sp_icon('user', '', 18) ?>
            <span>4. Human Face / Provider Avatar Mode</span>
        </h2>
    </div>
    <p style="color: var(--sp-text-secondary); margin-bottom: 14px;">
        Setting <code>mode: 'avatar'</code> applies the standardized specifications for service provider profile photos:
    </p>
    <ul class="sp-feature-list">
        <li><?= sp_icon('check', '', 16) ?><span><strong>Auto Aspect-Fill &amp; Crop:</strong> Converts wide landscape or tall portrait photos into a clean 1:1 square (600&times;600 px).</span></li>
        <li><?= sp_icon('check', '', 16) ?><span><strong>Optimal Face JPEG Compression:</strong> Encodes with high-fidelity 80% JPEG, preserving skin tones while shrinking payloads.</span></li>
        <li><?= sp_icon('check', '', 16) ?><span><strong>Timestamped Filename:</strong> Generates <code>prefix_YYYY-MM-DD_HH-mm-ss.jpg</code> (e.g. <code>provider_19_2026-11-26_12-30-45.jpg</code>) for cache-busting.</span></li>
    </ul>
</section>

<!-- 5. ScalePixel SDK Integration -->
<section class="sp-glass" id="scalepixel-sdk">
    <div class="sp-card-header">
        <h2 class="sp-card-title" style="font-size: 20px;">
            <?= sp_icon('code-2', '', 18) ?>
            <span>5. ScalePixel Client Integration SDKs</span>
        </h2>
    </div>
    <p style="color: var(--sp-text-secondary); margin-bottom: 16px;">
        Drop-in client libraries are available inside the <code>integrations/</code> directory:
    </p>

    <h4>React Native / Mobile App (TypeScript)</h4>
    <div class="sp-code-wrap">
        <pre class="sp-code" id="code-sdk-ts">import { ScalePixelClient } from './integrations/ScalePixelClient';

const scalePixel = new ScalePixelClient('http://localhost/ScalePixel/api/v1');

// Upload &amp; compress Provider Avatar in 1 line:
const result = await scalePixel.compressAvatar(selectedPhotoUri, providerId);
console.log('New Avatar URL:', result.data.download_url);
console.log('Saved:', result.data.saved_percent + '%');</pre>
        <button type="button" class="sp-btn sp-btn-sm" data-copy-target="code-sdk-ts" style="margin-top: 8px;">
            <?= sp_icon('copy', '', 14) ?>
            <span>Copy TypeScript</span>
        </button>
    </div>

    <h4 style="margin-top: 20px;">PHP Backend / Web Server</h4>
    <div class="sp-code-wrap">
        <pre class="sp-code" id="code-sdk-php">require_once 'integrations/ScalePixelClient.php';

$client = new ScalePixelClient();
$result = $client->compressAvatar('/tmp/uploaded_photo.jpg', 19);

if ($result['status'] === 'success') {
    $avatarUrl = $result['data']['download_url'];
    // Save $avatarUrl in MySQL providers table
}</pre>
        <button type="button" class="sp-btn sp-btn-sm" data-copy-target="code-sdk-php" style="margin-top: 8px;">
            <?= sp_icon('copy', '', 14) ?>
            <span>Copy PHP</span>
        </button>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
