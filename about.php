<?php
require_once __DIR__ . '/includes/icons.php';

$pageTitle = 'About ScalePixel - Architecture & Media Engine';
$activeNav = 'about';
$pageEyebrow = 'Version 1.0.0';
$pageHeading = 'About ScalePixel';
$pageSubtitle = 'Stateless, enterprise-grade media optimization architecture engineered for mobile apps and modern web platforms.';
$headerActionsHtml = '<a href="index.php" class="sp-btn sp-btn-primary">' . sp_icon('sliders-horizontal', '', 16) . '<span>Open Studio</span></a>';

require_once __DIR__ . '/includes/header.php';
?>

<article class="sp-glass sp-prose">
    <p>
        <strong>ScalePixel</strong> is a high-performance media optimization platform engineered to solve the bandwidth and storage bottlenecks of high-resolution smartphone cameras on modern mobile apps and web platforms.
    </p>

    <h2>Our Mission</h2>
    <p>
        Smartphones capture photos at 12 MP to 108 MP, generating raw files between 5 MB and 25 MB each. When millions of users upload those files directly, servers fill up, mobile bandwidth suffers, and interfaces lag.
    </p>
    <p>
        ScalePixel provides a stateless engine that shrinks large uploads dramatically without perceptible degradation in visual quality, keeping end-user loading fast.
    </p>

    <h2>Core Engineering Principles</h2>
    <ul>
        <li><strong>Zero Data Footprint:</strong> ScalePixel processes images dynamically and purges temporary output files within 60 minutes.</li>
        <li><strong>Intelligent Content Optimization:</strong> Dedicated modes for portraits (JPEG 80% fidelity, 600&times;600 square avatars) and modern web showcases (WebP / AVIF compression).</li>
        <li><strong>Universal Format Matrix:</strong> Any-to-any transcoding across JPG, PNG, WebP, AVIF, GIF, BMP, and ICO.</li>
        <li><strong>Serverless Cloud Scalability:</strong> Ready for containerized or AWS Lambda deployment with automated CI/CD workflows.</li>
    </ul>

    <h2>Technology Stack</h2>
    <ul>
        <li><strong>Core Processing:</strong> High-performance PHP GD with EXIF auto-rotation, truecolor resampling, and progressive encoding.</li>
        <li><strong>Security Inspection:</strong> Magic-byte verification, decompression-bomb limits, and polyglot payload rejection.</li>
        <li><strong>API Security:</strong> Dual-token Source Key and Secret Key authentication with per-IP rate limiting.</li>
        <li><strong>Integration:</strong> Native drop-in SDKs for PHP backends, TypeScript web apps, and React Native mobile clients.</li>
    </ul>

    <h2>Corporate Background</h2>
    <p>
        Engineered by the ScalePixel team to empower developers and high-growth platforms worldwide with reliable, privacy-first media processing infrastructure.
    </p>
</article>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
