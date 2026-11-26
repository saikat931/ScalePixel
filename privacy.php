<?php
$pageTitle = 'Privacy Policy - ScalePixel';
$activeNav = 'privacy';
require_once __DIR__ . '/includes/header.php';
?>

<div class="page-container">
    <div class="legal-card">
        <h1>Privacy Policy</h1>
        <div class="meta-date">Last Updated: November 26, 2026 • Effective Immediately</div>

        <p>
            At <strong>ScalePixel</strong>, accessible from our official domains and APIs, the privacy of our visitors and API consumers is of paramount importance. This Privacy Policy outlines the types of information we process and how it is protected under global privacy standards, including GDPR and CCPA.
        </p>

        <h2>1. Zero Persistent Data Storage Guarantee</h2>
        <p>
            Unlike traditional cloud storage providers, <strong>ScalePixel is a stateless media processing engine</strong>:
        </p>
        <ul>
            <li>Images uploaded to our interactive studio or REST API are processed in-memory.</li>
            <li>Any temporary intermediate files written to disk for downloading are strictly ephemeral and are <strong>permanently deleted within 60 minutes</strong> by our automated purge routines.</li>
            <li>We do <strong>not</strong> build facial recognition databases, sell image data, or retain personal photos beyond the immediate processing window.</li>
        </ul>

        <h2>2. Data Processed for API Operations</h2>
        <p>
            When utilizing our developer REST APIs, we collect minimal operational metadata:
        </p>
        <ul>
            <li>API Source Key and Secret Key for authentication.</li>
            <li>Aggregate usage metrics (total requests processed, bytes saved, timestamp of last activity).</li>
            <li>Network request IP addresses solely for DDoS prevention and rate-limiting.</li>
        </ul>

        <h2>3. Image Content & Copyright Ownership</h2>
        <p>
            You retain 100% intellectual property rights and copyright to any image you submit through ScalePixel. ScalePixel claims no rights, licenses, or ownership over your content.
        </p>

        <h2>4. Data Security</h2>
        <p>
            All communications with our endpoints occur over encrypted Transport Layer Security (TLS/HTTPS). Authentication credentials must be kept confidential by the account holder.
        </p>

        <h2>5. Contact Data Protection Officer</h2>
        <p>
            For privacy inquiries or compliance audits, contact our Data Protection Team at <a href="contact.php" style="color: var(--cyan);">privacy@scalepixel.io</a>.
        </p>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
