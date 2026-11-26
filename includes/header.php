<?php
if (!isset($pageTitle)) {
    $pageTitle = 'ScalePixel - Universal Image Compression & Conversion Engine';
}
if (!isset($activeNav)) {
    $activeNav = 'home';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <meta name="description" content="ScalePixel is a high-performance image compression and universal format conversion engine. Convert and compress JPG, PNG, WebP, AVIF with enterprise REST APIs.">
    <link rel="stylesheet" href="assets/css/scalepixel.css">
    <!-- Three.js for 3D Background -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
</head>
<body>
    <!-- 3D Canvas Background -->
    <canvas id="threejs-canvas"></canvas>

    <div class="content-wrapper">
        <!-- Navigation -->
        <nav class="navbar">
            <div class="nav-container">
                <a href="index.php" class="brand-logo">
                    <div class="logo-badge">⚡</div>
                    <div class="brand-text">Scale<span>Pixel</span></div>
                </a>

                <ul class="nav-links">
                    <li><a href="index.php" class="<?= $activeNav === 'home' ? 'active' : '' ?>">Studio</a></li>
                    <li><a href="api-dashboard.php" class="<?= $activeNav === 'keys' ? 'active' : '' ?>">API Keys</a></li>
                    <li><a href="docs.php" class="<?= $activeNav === 'docs' ? 'active' : '' ?>">Documentation</a></li>
                    <li><a href="about.php" class="<?= $activeNav === 'about' ? 'active' : '' ?>">About</a></li>
                    <li><a href="contact.php" class="<?= $activeNav === 'contact' ? 'active' : '' ?>">Contact</a></li>
                    <li><a href="api-dashboard.php" class="nav-btn">Get API Keys</a></li>
                </ul>
            </div>
        </nav>
