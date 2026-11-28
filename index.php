<?php
require_once __DIR__ . '/includes/icons.php';

$pageTitle = 'ScalePixel - Universal Image Compression & Conversion Engine';
$activeNav = 'home';
$pageEyebrow = 'Quantum Media Engine v1.0';
$pageHeading = 'Compress & convert any image';
$pageSubtitle = 'Fast, private image optimization. Transform heavy smartphone photos into lightweight payloads with zero persistent storage.';
$headerActionsHtml =
    '<a href="docs.php" class="sp-btn">' . sp_icon('book-open', '', 16) . '<span>API Docs</span></a>' .
    '<a href="api-dashboard.php" class="sp-btn sp-btn-primary">' . sp_icon('key', '', 16) . '<span>Get API Keys</span></a>';

require_once __DIR__ . '/includes/header.php';
?>

<!-- Mode Tabs -->
<div class="sp-tabs" role="tablist" aria-label="Optimization modes">
    <button type="button" class="sp-tab" role="tab" aria-selected="true" tabindex="0" data-mode="auto">
        <?= sp_icon('zap', '', 16) ?>
        <span>Auto Smart</span>
    </button>
    <button type="button" class="sp-tab" role="tab" aria-selected="false" tabindex="-1" data-mode="avatar">
        <?= sp_icon('user', '', 16) ?>
        <span>Profile Avatar</span>
    </button>
    <button type="button" class="sp-tab" role="tab" aria-selected="false" tabindex="-1" data-mode="showcase">
        <?= sp_icon('image', '', 16) ?>
        <span>Web Showcase</span>
    </button>
    <button type="button" class="sp-tab" role="tab" aria-selected="false" tabindex="-1" data-mode="convert">
        <?= sp_icon('refresh-cw', '', 16) ?>
        <span>Format Converter</span>
    </button>
    <button type="button" class="sp-tab" role="tab" aria-selected="false" tabindex="-1" data-mode="custom">
        <?= sp_icon('settings', '', 16) ?>
        <span>Custom</span>
    </button>
</div>

<!-- Studio Upload & Settings Grid -->
<section class="sp-studio-grid" aria-label="Image upload and optimization controls">
    <div class="sp-glass">
        <div
            class="sp-dropzone"
            id="drop-zone"
            role="button"
            tabindex="0"
            aria-label="Drop your images here or press Enter to browse files"
        >
            <div class="sp-upload-icon">
                <?= sp_icon('upload-cloud', '', 34) ?>
            </div>
            <h2>Drop your images here</h2>
            <p class="sp-helper">Supported: JPG, PNG, WEBP, AVIF, GIF, BMP, ICO &bull; Max 25 MB per file</p>
            <div class="sp-pill-row" aria-hidden="true">
                <span class="sp-pill">JPG</span>
                <span class="sp-pill">PNG</span>
                <span class="sp-pill">WEBP</span>
                <span class="sp-pill">AVIF</span>
                <span class="sp-pill">GIF</span>
                <span class="sp-pill">BMP</span>
                <span class="sp-pill">ICO</span>
            </div>
            <button type="button" class="sp-btn sp-btn-primary sp-btn-sm" id="browse-btn" style="margin-top: 18px;">
                <?= sp_icon('file-image', '', 15) ?>
                <span>Browse Files</span>
            </button>
            <input type="file" id="file-input" multiple accept="image/*" class="sp-sr-only">
        </div>
    </div>

    <div class="sp-glass">
        <div class="sp-card-header">
            <h3 class="sp-card-title">
                <?= sp_icon('sliders', '', 18) ?>
                <span>Optimization Settings</span>
            </h3>
            <span class="sp-badge sp-badge-accent">Batch Ready</span>
        </div>

        <div class="sp-form-stack">
            <div class="sp-field">
                <label class="sp-label" for="format-select">Target Format</label>
                <select id="format-select" class="sp-select">
                    <option value="auto">Auto Optimal Format</option>
                    <option value="jpeg">JPEG (.jpg) — Faces &amp; Photos</option>
                    <option value="webp">WebP (.webp) — Modern Web</option>
                    <option value="avif">AVIF (.avif) — Ultra High Density</option>
                    <option value="png">PNG (.png) — Graphics &amp; Alpha</option>
                    <option value="ico">ICO (.ico) — Favicon 32&times;32</option>
                </select>
            </div>

            <div class="sp-field">
                <label class="sp-label" for="quality-slider">Compression Quality</label>
                <div class="sp-range-row">
                    <input type="range" id="quality-slider" min="10" max="100" value="80" class="sp-range is-custom" aria-describedby="quality-val">
                    <span id="quality-val" class="sp-value-pill">80%</span>
                </div>
            </div>

            <div class="sp-field">
                <label class="sp-label" for="prefix-input">Filename Prefix</label>
                <input type="text" id="prefix-input" class="sp-input" placeholder="e.g. provider_19 or shop_22" value="provider_19">
                <span class="sp-helper" id="mode-hint">Auto Smart balances file size and clarity (WebP for PNG transparency, JPEG for photos).</span>
            </div>

            <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; font-size: 13px; color: var(--sp-text-secondary);">
                <input type="checkbox" id="include-seconds" class="sp-check" checked>
                <span>Include seconds in timestamped filename</span>
            </label>
        </div>
    </div>
</section>

<!-- Queue Metrics & Visual Comparison -->
<section class="sp-results-grid" aria-label="Compression queue and visual comparison">
    <div class="sp-glass">
        <div class="sp-card-header">
            <h3 class="sp-card-title">
                <?= sp_icon('gauge', '', 18) ?>
                <span>Processing Queue</span>
            </h3>
            <button type="button" class="sp-btn sp-btn-ghost sp-btn-sm" id="clear-queue-btn" disabled>
                <?= sp_icon('trash-2', '', 15) ?>
                <span>Clear</span>
            </button>
        </div>

        <div class="sp-metrics">
            <div class="sp-metric">
                <span class="sp-metric-label"><?= sp_icon('layers', '', 14) ?> Processed</span>
                <span class="sp-metric-value" id="metric-count">0</span>
            </div>
            <div class="sp-metric">
                <span class="sp-metric-label"><?= sp_icon('hard-drive', '', 14) ?> Saved</span>
                <span class="sp-metric-value is-compact" id="metric-saved">&mdash;</span>
            </div>
            <div class="sp-metric">
                <span class="sp-metric-label"><?= sp_icon('zap', '', 14) ?> Avg Speed</span>
                <span class="sp-metric-value" id="metric-speed">&mdash;</span>
            </div>
        </div>

        <div class="sp-empty" id="queue-empty">
            <?= sp_icon('file-image', '', 28) ?>
            <p style="font-weight: 500; color: var(--sp-text); margin-bottom: 4px;">No images queued yet</p>
            <p class="sp-helper">Upload or drop files above to view actual compression savings, execution speed, and downloads.</p>
        </div>

        <div class="sp-queue" id="results-container" aria-live="polite"></div>
    </div>

    <div class="sp-glass">
        <div class="sp-card-header">
            <h3 class="sp-card-title">
                <?= sp_icon('arrow-left-right', '', 18) ?>
                <span>Visual Fidelity Comparison</span>
            </h3>
            <span class="sp-helper">Drag or use arrow keys</span>
        </div>

        <div class="sp-compare-empty" id="compare-empty">
            <?= sp_icon('eye', '', 28) ?>
            <p style="font-weight: 500; color: var(--sp-text);">Ready to compare</p>
            <p class="sp-helper">Compress an image to inspect original vs. optimized pixels side-by-side.</p>
        </div>

        <div class="sp-compare" id="compare-box" hidden>
            <img id="after-img" src="" alt="ScalePixel compressed image">
            <img id="before-img" src="" class="sp-compare-before" alt="Original uploaded image">
            <span class="sp-image-label sp-image-label-left">
                <strong>Original</strong>
                <span id="compare-before-meta">&mdash;</span>
            </span>
            <span class="sp-image-label sp-image-label-right">
                <strong>ScalePixel</strong>
                <span id="compare-after-meta">&mdash;</span>
            </span>
            <div class="sp-compare-line" aria-hidden="true"></div>
            <div class="sp-compare-handle" aria-hidden="true">
                <?= sp_icon('arrow-left-right', '', 16) ?>
            </div>
            <input
                type="range"
                id="compare-range"
                class="sp-compare-range"
                min="5"
                max="95"
                value="50"
                aria-label="Before and after comparison split percentage"
            >
        </div>
    </div>
</section>

<!-- Developer API Plans -->
<section aria-labelledby="pricing-heading" id="pricing">
    <div style="display: grid; gap: 6px; margin-bottom: 16px;">
        <span class="sp-eyebrow"><?= sp_icon('key', '', 13) ?> Developer API Plans</span>
        <h2 id="pricing-heading">Transparent developer pricing</h2>
        <p style="color: var(--sp-text-secondary);">Instant API key activation from prototypes to dedicated enterprise throughput.</p>
    </div>

    <div class="sp-grid-3">
        <div class="sp-glass sp-pricing-card">
            <div style="display: grid; gap: 14px;">
                <div>
                    <h4>Developer Starter</h4>
                    <p class="sp-helper" style="margin-top: 4px;">Ideal for indie apps, prototypes, and personal tools.</p>
                </div>
                <div class="sp-price">&#8377;9 <span>/ month</span></div>
                <ul class="sp-feature-list">
                    <li><?= sp_icon('check', '', 16) ?><span><strong>10,000</strong> image requests / month</span></li>
                    <li><?= sp_icon('check', '', 16) ?><span>Max <strong>15 MB</strong> per upload file</span></li>
                    <li><?= sp_icon('check', '', 16) ?><span>Universal format matrix (JPG, WebP, PNG)</span></li>
                    <li><?= sp_icon('check', '', 16) ?><span>Profile Avatar 1:1 auto-crop</span></li>
                    <li><?= sp_icon('check', '', 16) ?><span>Anti-shell &amp; magic-byte protection</span></li>
                </ul>
            </div>
            <a href="api-dashboard.php?plan=starter_9" class="sp-btn">Get Starter Key (&#8377;9/mo)</a>
        </div>

        <div class="sp-glass sp-pricing-card is-featured">
            <div style="display: grid; gap: 14px;">
                <div style="display: flex; align-items: center; justify-content: space-between; gap: 8px;">
                    <h4>Pro Enterprise</h4>
                    <span class="sp-badge sp-badge-accent">Popular</span>
                </div>
                <p class="sp-helper">For commercial platforms, startups, and high-growth apps.</p>
                <div class="sp-price" style="color: var(--sp-accent);">&#8377;49 <span>/ month</span></div>
                <ul class="sp-feature-list">
                    <li><?= sp_icon('check', '', 16) ?><span><strong>100,000</strong> image requests / month</span></li>
                    <li><?= sp_icon('check', '', 16) ?><span>Max <strong>35 MB</strong> per upload file</span></li>
                    <li><?= sp_icon('check', '', 16) ?><span>Sub-50ms priority execution queue</span></li>
                    <li><?= sp_icon('check', '', 16) ?><span>AVIF &amp; WebP next-gen compression</span></li>
                    <li><?= sp_icon('check', '', 16) ?><span>Custom timestamp filename formats</span></li>
                    <li><?= sp_icon('check', '', 16) ?><span>Parallel batch upload support</span></li>
                </ul>
            </div>
            <a href="api-dashboard.php?plan=pro_49" class="sp-btn sp-btn-primary">Get Pro Key (&#8377;49/mo)</a>
        </div>

        <div class="sp-glass sp-pricing-card">
            <div style="display: grid; gap: 14px;">
                <div>
                    <h4>ScalePixel Dedicated Cloud</h4>
                    <p class="sp-helper" style="margin-top: 4px;">For high-scale enterprise platforms and SaaS ecosystems.</p>
                </div>
                <div class="sp-price">&#8377;199 <span>/ month</span></div>
                <ul class="sp-feature-list">
                    <li><?= sp_icon('check', '', 16) ?><span><strong>Unlimited</strong> compressions &amp; conversions</span></li>
                    <li><?= sp_icon('check', '', 16) ?><span>Dedicated isolated memory queue</span></li>
                    <li><?= sp_icon('check', '', 16) ?><span>PHP &amp; TypeScript Client SDKs</span></li>
                    <li><?= sp_icon('check', '', 16) ?><span>Zero persistent data footprint guarantee</span></li>
                    <li><?= sp_icon('check', '', 16) ?><span>99.99% Uptime SLA &amp; Developer Support</span></li>
                </ul>
            </div>
            <a href="api-dashboard.php?plan=dedicated" class="sp-btn">Deploy Dedicated Key</a>
        </div>
    </div>
</section>

<!-- Core Capabilities -->
<section aria-labelledby="capabilities-heading">
    <div style="display: grid; gap: 6px; margin-bottom: 16px;">
        <h2 id="capabilities-heading">Engine capabilities</h2>
        <p style="color: var(--sp-text-secondary);">Stateless, security-hardened media processing built for modern apps.</p>
    </div>

    <div class="sp-grid-3">
        <article class="sp-glass">
            <div class="sp-card-header">
                <h4 class="sp-card-title"><?= sp_icon('user', '', 18) ?><span>Face &amp; Portrait Mode</span></h4>
            </div>
            <p style="color: var(--sp-text-secondary);">Auto-crops avatars to a crisp 600&times;600 1:1 square and generates cache-busting filenames like <code>provider_19_2026-11-26_12-30.jpg</code>.</p>
        </article>

        <article class="sp-glass">
            <div class="sp-card-header">
                <h4 class="sp-card-title"><?= sp_icon('refresh-cw', '', 18) ?><span>Universal Format Matrix</span></h4>
            </div>
            <p style="color: var(--sp-text-secondary);">Any-to-any transcoding across JPG, PNG, WebP, AVIF, GIF, BMP, and ICO with alpha transparency preservation.</p>
        </article>

        <article class="sp-glass">
            <div class="sp-card-header">
                <h4 class="sp-card-title"><?= sp_icon('zap', '', 18) ?><span>In-Memory Speed</span></h4>
            </div>
            <p style="color: var(--sp-text-secondary);">High-speed truecolor resampling and quantization process high-resolution camera uploads in milliseconds.</p>
        </article>

        <article class="sp-glass">
            <div class="sp-card-header">
                <h4 class="sp-card-title"><?= sp_icon('key', '', 18) ?><span>Dual-Key REST API</span></h4>
            </div>
            <p style="color: var(--sp-text-secondary);">Authenticate cleanly via <code>X-ScalePixel-Key</code> &amp; <code>X-ScalePixel-Secret</code> headers or Bearer tokens.</p>
        </article>

        <article class="sp-glass">
            <div class="sp-card-header">
                <h4 class="sp-card-title"><?= sp_icon('shield-check', '', 18) ?><span>Zero-Data Ephemeral</span></h4>
            </div>
            <p style="color: var(--sp-text-secondary);">No permanent asset storage. Files are validated against polyglot payloads and auto-purged within 60 minutes.</p>
        </article>

        <article class="sp-glass">
            <div class="sp-card-header">
                <h4 class="sp-card-title"><?= sp_icon('smartphone', '', 18) ?><span>Drop-In SDKs</span></h4>
            </div>
            <p style="color: var(--sp-text-secondary);">Ready-to-use client integrations for PHP backends, TypeScript web apps, and React Native mobile apps.</p>
        </article>
    </div>
</section>

<!-- Benchmark Comparison Table -->
<section class="sp-glass" aria-labelledby="benchmark-heading">
    <div class="sp-card-header">
        <h3 class="sp-card-title" id="benchmark-heading">
            <?= sp_icon('layers', '', 18) ?>
            <span>Industry Benchmark Comparison</span>
        </h3>
    </div>
    <div class="sp-table-wrap">
        <table class="sp-table">
            <thead>
                <tr>
                    <th>FEATURE</th>
                    <th style="color: var(--sp-accent);">SCALEPIXEL</th>
                    <th>TINYPNG</th>
                    <th>CLOUDINARY</th>
                    <th>CLOUDCONVERT</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="font-weight: 600; color: var(--sp-text);">Face / Avatar Mode</td>
                    <td class="sp-state-success" style="font-weight: 600;">Native Auto-Crop</td>
                    <td>Manual only</td>
                    <td>Paid tier</td>
                    <td>Not supported</td>
                </tr>
                <tr>
                    <td style="font-weight: 600; color: var(--sp-text);">Custom Date-Time Naming</td>
                    <td class="sp-state-success" style="font-weight: 600;">YYYY-MM-DD_HH-mm</td>
                    <td>Random hash</td>
                    <td>Public ID</td>
                    <td>Standard name</td>
                </tr>
                <tr>
                    <td style="font-weight: 600; color: var(--sp-text);">Universal Any-to-Any Convert</td>
                    <td class="sp-state-success" style="font-weight: 600;">JPG, WebP, AVIF, PNG, ICO</td>
                    <td>JPG/PNG/WebP only</td>
                    <td>Complex syntax</td>
                    <td>Supported</td>
                </tr>
                <tr>
                    <td style="font-weight: 600; color: var(--sp-text);">Zero-Data Ephemeral Privacy</td>
                    <td class="sp-state-success" style="font-weight: 600;">0 Permanent Storage</td>
                    <td>48 hr purge</td>
                    <td>Stored permanently</td>
                    <td>24 hr purge</td>
                </tr>
                <tr>
                    <td style="font-weight: 600; color: var(--sp-text);">Self-Hosted / Cloud Freedom</td>
                    <td class="sp-state-success" style="font-weight: 600;">AWS Lambda / PHP Ready</td>
                    <td>Paid subscriptions</td>
                    <td>$89+/mo pricing</td>
                    <td>Credit-based billing</td>
                </tr>
            </tbody>
        </table>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
