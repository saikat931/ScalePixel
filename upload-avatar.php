<?php
require_once __DIR__ . '/includes/icons.php';

$providerId = (int)($_GET['provider_id'] ?? 0);
$providerName = htmlspecialchars(trim($_GET['name'] ?? 'Service Provider'), ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Provider Photo - ScalePixel</title>
    <link rel="preload" href="assets/fonts/poppins-400.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="assets/fonts/poppins-600.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="stylesheet" href="assets/css/scalepixel.css">
    <style>
        .sp-avatar-shell {
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 20px;
        }
        .sp-avatar-card {
            width: 100%;
            max-width: 420px;
            text-align: center;
            display: grid;
            gap: 16px;
        }
        .sp-avatar-preview-box {
            position: relative;
            width: 120px;
            height: 120px;
            margin: 4px auto;
        }
        .sp-avatar-img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--sp-accent);
            background: var(--sp-surface);
        }
        .sp-avatar-overlay {
            display: none;
            position: absolute;
            inset: 0;
            border-radius: 50%;
            background: rgba(8, 20, 8, 0.82);
            align-items: center;
            justify-content: center;
        }
    </style>
</head>
<body>
    <main class="sp-avatar-shell sp-app" style="display: grid;">
        <section class="sp-glass sp-avatar-card" aria-label="Provider avatar upload">
            <div style="display: flex; justify-content: center;">
                <span class="sp-eyebrow"><?= sp_icon('zap', '', 13) ?> ScalePixel Optimizer</span>
            </div>

            <div style="display: grid; gap: 6px;">
                <h2 style="font-size: 24px;">Update profile photo</h2>
                <p style="color: var(--sp-text-secondary); font-size: 13px;">
                    Take a photo or pick from your gallery. Optimized to 1:1 square JPEG (600&times;600).
                </p>
            </div>

            <div class="sp-avatar-preview-box">
                <img
                    id="avatarPreview"
                    class="sp-avatar-img"
                    src="https://ui-avatars.com/api/?name=<?= urlencode($providerName ?: 'SP') ?>&background=30481B&color=F5F8F2&size=160&bold=true"
                    alt="Provider avatar preview"
                >
                <div id="avatarLoading" class="sp-avatar-overlay">
                    <span class="sp-spinner" style="width: 28px; height: 28px;" aria-label="Uploading"></span>
                </div>
            </div>

            <input type="file" id="fileInput" class="sp-sr-only" accept="image/*">

            <button id="uploadBtn" type="button" class="sp-btn sp-btn-primary" style="width: 100%;" onclick="document.getElementById('fileInput').click()">
                <?= sp_icon('camera', '', 18) ?>
                <span>Take Photo or Pick Image</span>
            </button>

            <div id="statusBox" class="sp-alert" style="display: none; text-align: left;" role="status" aria-live="polite"></div>

            <a id="returnBtn" class="sp-btn" style="display: none; width: 100%;" href="javascript:void(0)" onclick="returnToApp()">
                <?= sp_icon('arrow-left', '', 16) ?>
                <span>Return to ServEasy Provider App</span>
            </a>

            <p class="sp-helper">
                Powered by ScalePixel Engine &bull; ServEasy Mobile API
            </p>
        </section>
    </main>

    <script>
        const providerId = <?= $providerId ?>;
        const fileInput = document.getElementById('fileInput');
        const uploadBtn = document.getElementById('uploadBtn');
        const avatarPreview = document.getElementById('avatarPreview');
        const avatarLoading = document.getElementById('avatarLoading');
        const statusBox = document.getElementById('statusBox');
        const returnBtn = document.getElementById('returnBtn');

        fileInput.addEventListener('change', async (e) => {
            const file = e.target.files[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = (re) => {
                avatarPreview.src = re.target.result;
            };
            reader.readAsDataURL(file);

            avatarLoading.style.display = 'flex';
            uploadBtn.disabled = true;
            statusBox.style.display = 'flex';
            statusBox.className = 'sp-alert sp-state-pending';
            statusBox.textContent = 'Optimizing photo with ScalePixel...';

            try {
                const formData = new FormData();
                formData.append('file', file);
                formData.append('mode', 'avatar');
                formData.append('prefix', 'provider_' + providerId);
                formData.append('include_seconds', '1');
                formData.append('format', 'jpeg');
                formData.append('quality', '80');
                formData.append('width', '600');
                formData.append('height', '600');
                formData.append('fit', 'cover');

                const spRes = await fetch('/api/v1/compress.php', {
                    method: 'POST',
                    headers: {
                        'X-ScalePixel-Key': 'sp_live_pk_scalepixel_starter',
                        'X-ScalePixel-Secret': 'sp_live_sk_a92d8f1e4c7b30129481bfe48102a991'
                    },
                    body: formData
                });

                const spJson = await spRes.json();
                if (spJson.status !== 'success' || !spJson.data || !spJson.data.download_url) {
                    throw new Error(spJson.message || 'Optimization failed on ScalePixel');
                }

                let finalDownloadUrl = spJson.data.download_url;
                if (finalDownloadUrl.startsWith('http://scalepixel.onrender.com')) {
                    finalDownloadUrl = finalDownloadUrl.replace('http://', 'https://');
                }

                avatarPreview.src = finalDownloadUrl;
                statusBox.textContent = 'Saving to your ServEasy account...';

                const backendEndpoints = [
                    'https://beige-curlew-610555.hostingersite.com/api/dealer-auth-api.php',
                    '/backend/api/dealer-auth-api.php'
                ];

                for (const endpoint of backendEndpoints) {
                    try {
                        const saveRes = await fetch(endpoint, {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({
                                action: 'update_avatar',
                                provider_id: providerId,
                                avatar_url: finalDownloadUrl
                            })
                        });
                        const saveJson = await saveRes.json();
                        if (saveJson.status === 'success') {
                            break;
                        }
                    } catch (err) {
                        console.warn('Backend endpoint failed:', endpoint, err);
                    }
                }

                avatarLoading.style.display = 'none';
                uploadBtn.disabled = false;
                uploadBtn.querySelector('span').textContent = 'Change Photo Again';

                const savedPercent = spJson.data.saved_percent ? Math.round(spJson.data.saved_percent) : 0;
                statusBox.className = 'sp-alert sp-state-success';
                statusBox.textContent = `Photo updated: compressed by ${savedPercent}% (${spJson.data.compressed_size_kb} KB).`;

                returnBtn.style.display = 'inline-flex';
            } catch (err) {
                avatarLoading.style.display = 'none';
                uploadBtn.disabled = false;
                statusBox.className = 'sp-alert sp-state-error';
                statusBox.textContent = 'Error: ' + (err.message || 'Failed to upload photo');
            }
        });

        function returnToApp() {
            window.location.href = 'serveasy-provider://';
            setTimeout(() => {
                alert('Switch back to the ServEasy Provider app — your new photo will display immediately!');
            }, 500);
        }
    </script>
</body>
</html>
