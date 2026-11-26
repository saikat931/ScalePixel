<?php
$pageTitle = 'About ScalePixel - Architecture & Media Engine';
$activeNav = 'about';
require_once __DIR__ . '/includes/header.php';
?>

<div class="page-container">
    <div class="legal-card">
        <h1>About ScalePixel</h1>
        <div class="meta-date">Version 1.0.0 • Enterprise Cloud Architecture</div>

        <p>
            <strong>ScalePixel</strong> is a modern, high-performance media optimization platform engineered to solve the data and performance bottlenecks of high-resolution smartphone cameras on modern mobile apps and web platforms.
        </p>

        <h2>Our Mission</h2>
        <p>
            Smartphones today capture photos at 12 MP to 108 MP, generating raw files between 5 MB and 25 MB each. When millions of users upload these files to web servers and mobile apps, servers run out of disk space, mobile bandwidth is consumed, and user experience lags.
        </p>
        <p>
            ScalePixel provides a stateless, sub-80ms engine that shrinks huge files by up to <strong>99.2%</strong> without perceptible degradation in visual quality, ensuring instant loading for end-users.
        </p>

        <h2>Core Engineering Principles</h2>
        <ul>
            <li><strong>0 Data Footprint:</strong> ScalePixel operates in-memory. Assets are processed dynamically and temporary caches are purged within 60 minutes.</li>
            <li><strong>Intelligent Content Optimization:</strong> Automatic routing for human portraits (JPEG 80% color fidelity) and graphics/landscapes (WebP / AVIF bandwidth compression).</li>
            <li><strong>Universal Format Matrix:</strong> Any-to-any transcoding across JPG, PNG, WebP, AVIF, GIF, BMP, and ICO.</li>
            <li><strong>Serverless Cloud Scalability:</strong> Ready for deployment on AWS Lambda with automated CI/CD from GitHub.</li>
        </ul>

        <h2>Technology Stack</h2>
        <ul>
            <li><strong>Core Processing:</strong> High-performance PHP GD with truecolor resampling & progressive encoding.</li>
            <li><strong>Frontend 3D Visuals:</strong> Three.js WebGL canvas scene with interactive dynamic particle field.</li>
            <li><strong>API Security:</strong> Double-token Source Key and Secret Key authentication protocol.</li>
            <li><strong>Integration:</strong> Native drop-in SDKs for Mobile Apps, React Native, iOS, Android & PHP.</li>
        </ul>

        <h2>Corporate Background</h2>
        <p>
            Engineered by the ScalePixel team to empower developers and high-growth platforms worldwide with scalable, enterprise-grade media processing infrastructure.
        </p>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
