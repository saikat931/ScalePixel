<?php
$pageTitle = 'Terms of Service - ScalePixel';
$activeNav = 'terms';
require_once __DIR__ . '/includes/header.php';
?>

<div class="page-container">
    <div class="legal-card">
        <h1>Terms of Service</h1>
        <div class="meta-date">Last Updated: November 26, 2026 • Effective Immediately</div>

        <p>
            Welcome to <strong>ScalePixel</strong>. By accessing our website, web tools, or developer REST APIs, you agree to comply with and be bound by the following terms and conditions.
        </p>

        <h2>1. Permitted Use</h2>
        <p>
            ScalePixel grants you a revocable, non-exclusive, non-transferable license to access our platform for lawful media processing and compression:
        </p>
        <ul>
            <li>You may use ScalePixel for personal, commercial, non-profit, or enterprise production workflows.</li>
            <li>You may integrate ScalePixel REST APIs into mobile applications, websites, content management systems, and backend servers.</li>
        </ul>

        <h2>2. Prohibited Content & Activities</h2>
        <p>
            You agree not to utilize ScalePixel to process, transmit, or store:
        </p>
        <ul>
            <li>Content that is unlawful, abusive, defamatory, or violates child exploitation laws.</li>
            <li>Malicious code, polyglot image exploits, steganographic malware, or DDoS attack payloads.</li>
            <li>Content that infringes upon third-party copyrights, patents, or trademarks without permission.</li>
        </ul>

        <h2>3. Fair Usage & Rate Limiting</h2>
        <p>
            Free developer keys are subject to fair use caps. Any attempt to bypass rate limits or compromise server infrastructure through botnets will result in immediate API key revocation and IP blocking.
        </p>

        <h2>4. Service Availability & Modifications</h2>
        <p>
            ScalePixel strives for 99.99% uptime. However, services are provided on an "AS IS" and "AS AVAILABLE" basis. We reserve the right to modify, upgrade, or deprecate endpoints with reasonable developer notice.
        </p>

        <h2>5. Termination</h2>
        <p>
            We reserve the right to suspend or terminate API keys that violate these terms without prior notice.
        </p>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
