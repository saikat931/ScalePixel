<?php
require_once __DIR__ . '/icons.php';

if (!isset($pageTitle)) {
    $pageTitle = 'ScalePixel - Universal Image Compression & Conversion Engine';
}
if (!isset($activeNav)) {
    $activeNav = 'home';
}
if (!isset($pageHeading)) {
    $pageHeading = 'Compress & convert any image';
}
if (!isset($pageSubtitle)) {
    $pageSubtitle = 'Fast, private image optimization.';
}

$navItems = [
    ['id' => 'home', 'href' => 'index.php', 'label' => 'Studio', 'icon' => 'sliders-horizontal'],
    ['id' => 'keys', 'href' => 'api-dashboard.php', 'label' => 'API Keys', 'icon' => 'key'],
    ['id' => 'docs', 'href' => 'docs.php', 'label' => 'Documentation', 'icon' => 'book-open'],
    ['id' => 'about', 'href' => 'about.php', 'label' => 'About', 'icon' => 'info'],
    ['id' => 'contact', 'href' => 'contact.php', 'label' => 'Contact', 'icon' => 'mail'],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></title>
    <meta name="description" content="ScalePixel is a high-performance image compression and universal format conversion engine. Convert and compress JPG, PNG, WebP, AVIF with enterprise REST APIs.">
    <link rel="preload" href="assets/fonts/poppins-400.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="assets/fonts/poppins-600.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="assets/fonts/montserrat.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="stylesheet" href="assets/css/scalepixel.css">
</head>
<body>
    <a href="#sp-main-content" class="sp-skip-link">Skip to main content</a>

    <div class="sp-app">
        <div class="sp-sidebar-scrim" id="sp-sidebar-scrim" hidden></div>

        <aside class="sp-sidebar" id="sp-sidebar" aria-label="Primary navigation">
            <div class="sp-sidebar-top">
                <div class="sp-sidebar-top-row">
                    <a href="index.php" class="sp-logo" aria-label="ScalePixel home">
                        <span class="sp-logo-badge"><?= sp_icon('zap', '', 20) ?></span>
                        <span class="sp-logo-text">Scale<span>Pixel</span></span>
                    </a>
                    <button
                        type="button"
                        class="sp-btn sp-btn-ghost sp-btn-icon sp-sidebar-close"
                        id="sp-sidebar-close"
                        aria-label="Close navigation menu"
                    >
                        <?= sp_icon('x', '', 20) ?>
                    </button>
                </div>

                <nav aria-label="ScalePixel sections">
                    <ul class="sp-nav">
                        <?php foreach ($navItems as $item): ?>
                            <?php $isActive = ($activeNav === $item['id']); ?>
                            <li>
                                <a
                                    href="<?= htmlspecialchars($item['href'], ENT_QUOTES, 'UTF-8') ?>"
                                    class="sp-nav-link"
                                    title="<?= htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8') ?>"
                                    aria-label="<?= htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8') ?>"
                                    <?= $isActive ? 'aria-current="page"' : '' ?>
                                >
                                    <?= sp_icon($item['icon'], '', 18) ?>
                                    <span class="sp-nav-text"><?= htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8') ?></span>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </nav>
            </div>

            <div class="sp-sidebar-footer">
                <div class="sp-status-pill">
                    <span class="sp-status-dot" aria-hidden="true"></span>
                    <span>Zero-Data Engine Active</span>
                </div>
                <div class="sp-sidebar-legal">
                    <a href="privacy.php" <?= $activeNav === 'privacy' ? 'aria-current="page"' : '' ?>>Privacy</a>
                    <a href="terms.php" <?= $activeNav === 'terms' ? 'aria-current="page"' : '' ?>>Terms</a>
                    <a href="disclaimer.php" <?= $activeNav === 'disclaimer' ? 'aria-current="page"' : '' ?>>Disclaimer</a>
                </div>
            </div>
        </aside>

        <main class="sp-main" id="sp-main-content" tabindex="-1">
            <header class="sp-header">
                <div class="sp-header-lead">
                    <button
                        type="button"
                        class="sp-btn sp-btn-icon sp-mobile-toggle"
                        id="sp-mobile-toggle"
                        aria-label="Open navigation menu"
                        aria-controls="sp-sidebar"
                        aria-expanded="false"
                    >
                        <?= sp_icon('menu', '', 20) ?>
                    </button>
                    <div class="sp-header-copy">
                        <?php if (!empty($pageEyebrow)): ?>
                            <span class="sp-eyebrow"><?= sp_icon('sparkles', '', 13) ?> <?= htmlspecialchars($pageEyebrow, ENT_QUOTES, 'UTF-8') ?></span>
                        <?php endif; ?>
                        <h1><?= htmlspecialchars($pageHeading, ENT_QUOTES, 'UTF-8') ?></h1>
                        <?php if (!empty($pageSubtitle)): ?>
                            <p><?= htmlspecialchars($pageSubtitle, ENT_QUOTES, 'UTF-8') ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if (!empty($headerActionsHtml)): ?>
                    <div class="sp-header-actions">
                        <?= $headerActionsHtml ?>
                    </div>
                <?php endif; ?>
            </header>
