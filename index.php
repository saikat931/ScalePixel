<?php
$pageTitle = 'ScalePixel - Universal Image Compression & Conversion Engine';
$activeNav = 'home';
require_once __DIR__ . '/includes/header.php';
?>

<!-- Hero Section -->
<section class="hero-section">
    <div class="hero-pill">
        <span>⚡</span> Quantum Media Processing Engine v1.0
    </div>
    <h1 class="hero-title">
        Compress & Convert Any Image<br>
        <span class="glow-gradient">In Under 80 Milliseconds</span>
    </h1>
    <p class="hero-subtitle">
        Enterprise-grade image optimization. Transform heavy 10 MB smartphone photos into feather-light, razor-sharp 75 KB payloads with 0 data storage footprint.
    </p>
</section>

<!-- Studio Interactive Upload Zone -->
<div class="studio-container">
    <div class="glass-card">
        <!-- Mode Tabs -->
        <div class="mode-tabs">
            <button class="mode-tab active" data-mode="auto">
                <span>⚡</span> Auto Smart
            </button>
            <button class="mode-tab" data-mode="avatar">
                <span>👤</span> Profile Avatar Mode
            </button>
            <button class="mode-tab" data-mode="showcase">
                <span>🖼️</span> Web Showcase (WebP)
            </button>
            <button class="mode-tab" data-mode="convert">
                <span>🔄</span> Format Converter
            </button>
            <button class="mode-tab" data-mode="custom">
                <span>⚙️</span> Custom Parameters
            </button>
        </div>

        <!-- Drop Zone -->
        <div class="drop-zone" id="drop-zone">
            <div class="drop-icon-wrap">⇪</div>
            <div class="drop-title">Drop your images here or click to browse</div>
            <div class="drop-sub">Fast client-to-engine processing • Max 25 MB per file • Batch uploads supported</div>
            <div class="drop-badge-list">
                <span class="format-pill">JPG</span>
                <span class="format-pill">PNG</span>
                <span class="format-pill">WEBP</span>
                <span class="format-pill">AVIF</span>
                <span class="format-pill">GIF</span>
                <span class="format-pill">BMP</span>
                <span class="format-pill">ICO</span>
            </div>
            <input type="file" id="file-input" multiple accept="image/*" style="display: none;">
        </div>

        <!-- Controls Grid -->
        <div class="controls-grid">
            <div class="control-item">
                <label>Target Format</label>
                <select id="format-select" class="control-select">
                    <option value="auto">Auto Optimal Format</option>
                    <option value="jpeg">JPEG (.jpg) - Faces & Photos</option>
                    <option value="webp">WebP (.webp) - Modern Web</option>
                    <option value="avif">AVIF (.avif) - Ultra High Density</option>
                    <option value="png">PNG (.png) - Graphics & Alpha</option>
                    <option value="ico">ICO (.ico) - Favicon 32x32</option>
                </select>
            </div>

            <div class="control-item">
                <label>Compression Quality</label>
                <div class="range-wrap">
                    <input type="range" id="quality-slider" min="10" max="100" value="80" class="control-range">
                    <span id="quality-val" class="range-val">80%</span>
                </div>
            </div>

            <div class="control-item">
                <label>Filename Prefix (e.g. provider_19)</label>
                <input type="text" id="prefix-input" class="control-input" placeholder="e.g. provider_19 or shop_22" value="provider_19">
            </div>
        </div>

        <!-- Results List -->
        <div class="results-container" id="results-container">
            <!-- Dynamically populated by studio.js -->
        </div>
    </div>
</div>

<!-- Interactive Before / After Split Slider -->
<section class="comparison-section">
    <div style="text-align: center; margin-bottom: 24px;">
        <h2 style="font-size: 26px; font-weight: 800; margin-bottom: 6px;">
            Visual Fidelity Comparison
        </h2>
        <p style="color: var(--text-secondary); font-size: 14px;">
            Drag the cyan center divider to compare the 10 MB original photo vs. the 75 KB ScalePixel compressed image.
        </p>
    </div>

    <div class="comparison-box" id="compare-box">
        <!-- Before Image (Original) -->
        <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=1200&q=100" class="compare-img" alt="Original Uncompressed">
        <span class="badge-tag before-tag">Original: 8.4 MB (Raw)</span>

        <!-- After Image (Compressed) -->
        <div class="compare-img after" id="compare-after">
            <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=1200&q=80" id="after-img" class="compare-img" alt="ScalePixel Compressed">
            <span class="badge-tag after-tag">ScalePixel: 72 KB (-99.1%)</span>
        </div>

        <!-- Draggable Handle -->
        <div class="compare-handle" id="compare-handle">
            <div class="handle-circle">↔</div>
        </div>
    </div>
</section>

<!-- Developer API Pricing Plans (₹9 and ₹49) -->
<section class="pricing-section" id="pricing" style="max-width: 1140px; margin: 0 auto 60px; padding: 0 20px;">
    <div style="text-align: center; margin-bottom: 24px;">
        <div class="hero-pill" style="margin-bottom: 8px;">ScalePixel Developer Pricing</div>
        <h2 style="font-size: 28px; font-weight: 900; letter-spacing: -0.5px; margin-bottom: 6px;">
            Affordable Developer API Plans
        </h2>
        <p style="color: var(--text-secondary); font-size: 14px; max-width: 600px; margin: 0 auto;">
            Transparent developer pricing with instant API key activation. Scale from prototypes to enterprise production.
        </p>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 24px;">
        <!-- Plan 1: ₹9 / month -->
        <div class="glass-card" style="display: flex; flex-direction: column; justify-content: space-between;">
            <div>
                <div style="font-size: 18px; font-weight: 800; color: #FFF; margin-bottom: 4px;">Developer Starter</div>
                <div style="font-size: 13px; color: var(--text-secondary); margin-bottom: 16px;">Ideal for indie hackers, small apps, and personal projects.</div>
                <div style="font-size: 36px; font-weight: 900; color: var(--cyan); margin-bottom: 20px;">
                    ₹9 <span style="font-size: 14px; color: var(--text-muted); font-weight: 600;">/ month</span>
                </div>
                <ul style="list-style: none; font-size: 13px; color: var(--text-secondary); line-height: 2; margin-bottom: 24px;">
                    <li>✓ <strong>10,000</strong> image requests / month</li>
                    <li>✓ Max <strong>15 MB</strong> per upload file</li>
                    <li>✓ Universal format matrix (JPG, WebP, PNG)</li>
                    <li>✓ Profile Avatar 1:1 auto-crop</li>
                    <li>✓ Anti-shell & magic-byte protection</li>
                </ul>
            </div>
            <a href="api-dashboard.php?plan=starter_9" class="nav-btn" style="display: block; text-align: center; text-decoration: none; padding: 12px;">Get Starter Key (₹9/mo)</a>
        </div>

        <!-- Plan 2: ₹49 / month (Featured) -->
        <div class="glass-card" style="display: flex; flex-direction: column; justify-content: space-between; border-color: rgba(0, 242, 254, 0.5); position: relative; overflow: hidden;">
            <div style="position: absolute; top: 0; right: 0; background: linear-gradient(135deg, var(--cyan), var(--blue)); color: #07090E; font-size: 10px; font-weight: 900; padding: 4px 16px; border-bottom-left-radius: 8px; text-transform: uppercase;">
                MOST POPULAR
            </div>
            <div>
                <div style="font-size: 18px; font-weight: 800; color: #FFF; margin-bottom: 4px;">Pro Enterprise</div>
                <div style="font-size: 13px; color: var(--text-secondary); margin-bottom: 16px;">For commercial platforms, startups, and high-growth services.</div>
                <div style="font-size: 36px; font-weight: 900; color: var(--emerald); margin-bottom: 20px;">
                    ₹49 <span style="font-size: 14px; color: var(--text-muted); font-weight: 600;">/ month</span>
                </div>
                <ul style="list-style: none; font-size: 13px; color: var(--text-secondary); line-height: 2; margin-bottom: 24px;">
                    <li>✓ <strong>100,000</strong> image requests / month</li>
                    <li>✓ Max <strong>35 MB</strong> per upload file</li>
                    <li>✓ Sub-50ms priority execution queue</li>
                    <li>✓ AVIF & WebP next-gen compression</li>
                    <li>✓ Custom timestamp filename formats</li>
                    <li>✓ Parallel batch upload support</li>
                </ul>
            </div>
            <a href="api-dashboard.php?plan=pro_49" class="nav-btn" style="display: block; text-align: center; text-decoration: none; padding: 12px; background: linear-gradient(135deg, var(--emerald), #00b4d8);">Get Pro Key (₹49/mo)</a>
        </div>

        <!-- Plan 3: ScalePixel Dedicated Cloud -->
        <div class="glass-card" style="display: flex; flex-direction: column; justify-content: space-between;">
            <div>
                <div style="font-size: 18px; font-weight: 800; color: #FFF; margin-bottom: 4px;">ScalePixel Dedicated Cloud</div>
                <div style="font-size: 13px; color: var(--text-secondary); margin-bottom: 16px;">For high-scale enterprise platforms and SaaS ecosystems.</div>
                <div style="font-size: 36px; font-weight: 900; color: var(--purple); margin-bottom: 20px;">
                    ₹199 <span style="font-size: 14px; color: var(--text-muted); font-weight: 600;">/ month</span>
                </div>
                <ul style="list-style: none; font-size: 13px; color: var(--text-secondary); line-height: 2; margin-bottom: 24px;">
                    <li>✓ <strong>Unlimited</strong> compressions & conversions</li>
                    <li>✓ Dedicated high-concurrency isolated memory queue</li>
                    <li>✓ ScalePixel Client SDKs (PHP & TypeScript)</li>
                    <li>✓ Zero persistent data footprint guarantee</li>
                    <li>✓ 99.99% Uptime SLA & Dedicated Developer Support</li>
                </ul>
            </div>
            <a href="api-dashboard.php?plan=dedicated" class="nav-btn" style="display: block; text-align: center; text-decoration: none; padding: 12px;">Deploy Dedicated Key ➔</a>
        </div>
    </div>
</section>

<!-- Features Grid -->
<section class="features-grid">
    <div class="feature-card">
        <div class="feature-icon">👤</div>
        <h3 class="feature-title">Human Face & Portrait Mode</h3>
        <p class="feature-desc">
            Engineered for profile avatars. Automatically centers faces, crops to a crisp 600×600 1:1 square, and generates timestamped names like <code>provider_19_2026-11-26_12-30.jpg</code>.
        </p>
    </div>

    <div class="feature-card">
        <div class="feature-icon">🔄</div>
        <h3 class="feature-title">Universal Format Matrix</h3>
        <p class="feature-desc">
            Seamless any-to-any conversion between JPG, PNG, WebP, AVIF, GIF, BMP, and ICO with full alpha transparency preservation.
        </p>
    </div>

    <div class="feature-card">
        <div class="feature-icon">⚡</div>
        <h3 class="feature-title">Sub-80ms In-Memory Speed</h3>
        <p class="feature-desc">
            Processing happens purely in memory. Heavy 12 MP camera files are resized and quantized in under 80 milliseconds.
        </p>
    </div>

    <div class="feature-card">
        <div class="feature-icon">🔑</div>
        <h3 class="feature-title">Source Key & Secret Key APIs</h3>
        <p class="feature-desc">
            REST endpoints authenticated via Bearer tokens or HTTP headers for seamless backend and mobile app integration.
        </p>
    </div>

    <div class="feature-card">
        <div class="feature-icon">🛡️</div>
        <h3 class="feature-title">0 Data Ephemeral Guarantee</h3>
        <p class="feature-desc">
            No permanent database or storage footprint. Uploaded assets are processed and automatically purged by our high-frequency cron.
        </p>
    </div>

    <div class="feature-card">
        <div class="feature-icon">📱</div>
        <h3 class="feature-title">ScalePixel Native SDKs</h3>
        <p class="feature-desc">
            Pre-built drop-in SDKs for PHP, Node.js, and React Native mobile applications.
        </p>
    </div>
</section>

<!-- Benchmark Comparison Table -->
<div class="studio-container" style="margin-top: 20px;">
    <div class="glass-card">
        <h3 style="font-size: 20px; font-weight: 800; margin-bottom: 16px; text-align: center;">
            Industry Benchmark Comparison
        </h3>
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; font-size: 13px; text-align: left;">
                <thead>
                    <tr style="border-bottom: 1px solid var(--border-subtle); color: var(--cyan);">
                        <th style="padding: 12px 16px;">FEATURE</th>
                        <th style="padding: 12px 16px;">SCALEPIXEL</th>
                        <th style="padding: 12px 16px; color: var(--text-secondary);">TINYPNG</th>
                        <th style="padding: 12px 16px; color: var(--text-secondary);">CLOUDINARY</th>
                        <th style="padding: 12px 16px; color: var(--text-secondary);">CLOUDCONVERT</th>
                    </tr>
                </thead>
                <tbody style="color: var(--text-secondary);">
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.04);">
                        <td style="padding: 12px 16px; font-weight: 700; color: #FFF;">Face / Avatar Mode</td>
                        <td style="padding: 12px 16px; color: var(--emerald); font-weight: 700;">✓ Native Auto-Crop</td>
                        <td style="padding: 12px 16px;">✗ Manual only</td>
                        <td style="padding: 12px 16px;">✓ Paid tier</td>
                        <td style="padding: 12px 16px;">✗ Not supported</td>
                    </tr>
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.04);">
                        <td style="padding: 12px 16px; font-weight: 700; color: #FFF;">Custom Date-Time Naming</td>
                        <td style="padding: 12px 16px; color: var(--emerald); font-weight: 700;">✓ YYYY-MM-DD_HH-mm</td>
                        <td style="padding: 12px 16px;">✗ Random hash</td>
                        <td style="padding: 12px 16px;">✗ Public ID</td>
                        <td style="padding: 12px 16px;">✗ Standard name</td>
                    </tr>
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.04);">
                        <td style="padding: 12px 16px; font-weight: 700; color: #FFF;">Universal Any-to-Any Convert</td>
                        <td style="padding: 12px 16px; color: var(--emerald); font-weight: 700;">✓ JPG, WebP, AVIF, PNG, ICO</td>
                        <td style="padding: 12px 16px;">✗ JPG/PNG/WebP only</td>
                        <td style="padding: 12px 16px;">✓ Complex syntax</td>
                        <td style="padding: 12px 16px;">✓ Supported</td>
                    </tr>
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.04);">
                        <td style="padding: 12px 16px; font-weight: 700; color: #FFF;">Zero-Data Ephemeral Privacy</td>
                        <td style="padding: 12px 16px; color: var(--emerald); font-weight: 700;">✓ 0 Permanent Storage</td>
                        <td style="padding: 12px 16px;">✓ 48 hr purge</td>
                        <td style="padding: 12px 16px;">✗ Stored permanently</td>
                        <td style="padding: 12px 16px;">✓ 24 hr purge</td>
                    </tr>
                    <tr>
                        <td style="padding: 12px 16px; font-weight: 700; color: #FFF;">Self-Hosted / Own Cloud Freedom</td>
                        <td style="padding: 12px 16px; color: var(--emerald); font-weight: 700;">✓ 100% Free Forever (AWS Lambda / PHP)</td>
                        <td style="padding: 12px 16px;">✗ Paid subscriptions</td>
                        <td style="padding: 12px 16px;">✗ \$89+/mo pricing</td>
                        <td style="padding: 12px 16px;">✗ Credit-based billing</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
