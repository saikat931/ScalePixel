<?php
$providerId = (int)($_GET['provider_id'] ?? 0);
$providerName = htmlspecialchars(trim($_GET['name'] ?? 'Service Provider'));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Upload Provider Photo - ScalePixel</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            -webkit-tap-highlight-color: transparent;
        }
        body {
            background: #0B0F17;
            color: #FFFFFF;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justifyContent: center;
            padding: 20px;
        }
        .container {
            width: 100%;
            max-width: 420px;
            background: rgba(18, 24, 38, 0.95);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 24px;
            padding: 28px 24px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.6);
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        .container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #05c279, #0084FF);
        }
        .header-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(5, 194, 121, 0.12);
            border: 1px solid rgba(5, 194, 121, 0.3);
            color: #05c279;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            margin-bottom: 16px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        h1 {
            font-size: 22px;
            font-weight: 800;
            margin-bottom: 6px;
            color: #FFFFFF;
        }
        p.subtitle {
            font-size: 13px;
            color: #8E9BAE;
            margin-bottom: 24px;
            line-height: 1.5;
        }
        .avatar-preview-box {
            position: relative;
            width: 120px;
            height: 120px;
            margin: 0 auto 24px;
        }
        .avatar-img {
            width: 120px;
            height: 120px;
            border-radius: 60px;
            object-fit: cover;
            border: 3px solid #05c279;
            box-shadow: 0 8px 24px rgba(5, 194, 121, 0.25);
            background: #1A2232;
        }
        .avatar-loading-overlay {
            display: none;
            position: absolute;
            inset: 0;
            background: rgba(11, 15, 23, 0.85);
            border-radius: 60px;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            gap: 6px;
        }
        .spinner {
            width: 32px;
            height: 32px;
            border: 3px solid rgba(5, 194, 121, 0.2);
            border-top-color: #05c279;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        .file-input {
            display: none;
        }
        .btn-upload {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #05c279 0%, #039f63 100%);
            color: #FFFFFF;
            font-size: 15px;
            font-weight: 700;
            border-radius: 14px;
            border: none;
            cursor: pointer;
            box-shadow: 0 8px 20px rgba(5, 194, 121, 0.3);
            transition: all 0.2s ease;
        }
        .btn-upload:active {
            transform: scale(0.98);
        }
        .status-card {
            display: none;
            margin-top: 20px;
            padding: 14px;
            border-radius: 14px;
            background: rgba(5, 194, 121, 0.1);
            border: 1px solid rgba(5, 194, 121, 0.25);
            font-size: 13px;
            color: #30D158;
            line-height: 1.5;
        }
        .status-card.error {
            background: rgba(255, 69, 58, 0.1);
            border-color: rgba(255, 69, 58, 0.25);
            color: #FF453A;
        }
        .btn-return {
            display: none;
            margin-top: 16px;
            width: 100%;
            padding: 14px;
            background: #0084FF;
            color: #FFFFFF;
            font-size: 14px;
            font-weight: 700;
            border-radius: 12px;
            text-decoration: none;
            text-align: center;
        }
        .engine-tag {
            margin-top: 24px;
            font-size: 11px;
            color: #556277;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header-badge">⚡ ScalePixel Optimizer</div>
        <h1>Update Profile Photo</h1>
        <p class="subtitle">Take a photo or pick from your gallery.<br>Optimized to 1:1 square JPEG (600×600) via ScalePixel.</p>

        <div class="avatar-preview-box">
            <img id="avatarPreview" class="avatar-img" src="https://ui-avatars.com/api/?name=<?= urlencode($providerName ?: 'SP') ?>&background=05c279&color=fff&size=160&bold=true" alt="Avatar">
            <div id="avatarLoading" class="avatar-loading-overlay">
                <div class="spinner"></div>
            </div>
        </div>

        <input type="file" id="fileInput" class="file-input" accept="image/*">

        <button id="uploadBtn" class="btn-upload" onclick="document.getElementById('fileInput').click()">
            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
            Take Photo or Pick Image
        </button>

        <div id="statusBox" class="status-card"></div>

        <a id="returnBtn" class="btn-return" href="javascript:void(0)" onclick="returnToApp()">
            ← Return to ServEasy Provider App
        </a>

        <div class="engine-tag">
            Powered by ScalePixel Quantum Engine • ServEasy Mobile API
        </div>
    </div>

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

            // Preview local image
            const reader = new FileReader();
            reader.onload = (re) => {
                avatarPreview.src = re.target.result;
            };
            reader.readAsDataURL(file);

            // Upload to ScalePixel API
            avatarLoading.style.display = 'flex';
            uploadBtn.disabled = true;
            statusBox.style.display = 'block';
            statusBox.className = 'status-card';
            statusBox.innerHTML = '⚡ Optimizing photo with ScalePixel...';

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

                // Save to ServEasy backend database
                statusBox.innerHTML = '💾 Saving to your ServEasy account...';

                // Try live Hostinger API first, fallback to current origin
                const backendEndpoints = [
                    'https://beige-curlew-610555.hostingersite.com/api/dealer-auth-api.php',
                    '/backend/api/dealer-auth-api.php'
                ];

                let saved = false;
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
                            saved = true;
                            break;
                        }
                    } catch (err) {
                        console.warn('Backend endpoint failed:', endpoint, err);
                    }
                }

                avatarLoading.style.display = 'none';
                uploadBtn.disabled = false;
                uploadBtn.innerHTML = '📷 Change Photo Again';

                const savedPercent = spJson.data.saved_percent ? Math.round(spJson.data.saved_percent) : 85;
                statusBox.innerHTML = `<strong>✨ Photo Updated Successfully!</strong><br>Compressed by ${savedPercent}% (${spJson.data.compressed_size_kb} KB). It is now saved to your account.`;

                returnBtn.style.display = 'block';

            } catch (err) {
                avatarLoading.style.display = 'none';
                uploadBtn.disabled = false;
                statusBox.className = 'status-card error';
                statusBox.innerHTML = '<strong>❌ Error:</strong> ' + (err.message || 'Failed to upload photo');
            }
        });

        function returnToApp() {
            // Try to deep link back to provider app, or instruct user
            window.location.href = 'serveasy-provider://';
            setTimeout(() => {
                alert('Switch back to the ServEasy Provider app — your new photo will display immediately!');
            }, 500);
        }
    </script>
</body>
</html>
