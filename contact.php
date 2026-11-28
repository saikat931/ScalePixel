<?php
require_once __DIR__ . '/includes/icons.php';

$sentSuccess = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'])) {
    $sentSuccess = true;
}

$pageTitle = 'Contact Support & Enterprise Inquiries - ScalePixel';
$activeNav = 'contact';
$pageEyebrow = 'Support & SLA Assistance';
$pageHeading = 'Contact & developer support';
$pageSubtitle = 'Questions about custom rate limits, AWS Lambda deployment, or mobile SDK integration? Reach our engineering team.';

require_once __DIR__ . '/includes/header.php';
?>

<div class="sp-grid-2">
    <section class="sp-glass">
        <div class="sp-card-header">
            <h2 class="sp-card-title" style="font-size: 20px;">
                <?= sp_icon('mail', '', 18) ?>
                <span>Send an Inquiry</span>
            </h2>
        </div>

        <?php if ($sentSuccess): ?>
            <div class="sp-alert sp-state-success" role="status" style="margin-bottom: 20px;">
                <?= sp_icon('check-circle-2', '', 18) ?>
                <span>Thank you! Your inquiry has been received. Our engineering team typically responds within 2 business hours.</span>
            </div>
        <?php endif; ?>

        <form method="POST" action="contact.php" class="sp-form-stack">
            <div class="sp-field">
                <label class="sp-label" for="contact_name">Your Name</label>
                <input type="text" id="contact_name" name="name" required placeholder="e.g. John Doe" class="sp-input">
            </div>

            <div class="sp-field">
                <label class="sp-label" for="contact_email">Email Address</label>
                <input type="email" id="contact_email" name="email" required placeholder="e.g. developer@company.com" class="sp-input">
            </div>

            <div class="sp-field">
                <label class="sp-label" for="contact_subject">Subject / Inquiry Type</label>
                <select id="contact_subject" name="subject" class="sp-select">
                    <option value="Integration">Platform &amp; Mobile App SDK Integration</option>
                    <option value="API Keys">API Keys &amp; Quota Limit Increase</option>
                    <option value="AWS Lambda">AWS Lambda / GitHub Auto-Deploy</option>
                    <option value="Bug Report">Technical Bug Report</option>
                    <option value="General">General Inquiry</option>
                </select>
            </div>

            <div class="sp-field">
                <label class="sp-label" for="contact_message">Message</label>
                <textarea id="contact_message" name="message" required rows="5" placeholder="How can we assist your integration?" class="sp-textarea"></textarea>
            </div>

            <button type="submit" class="sp-btn sp-btn-primary" style="width: 100%;">
                <?= sp_icon('send', '', 16) ?>
                <span>Send Message</span>
            </button>
        </form>
    </section>

    <aside class="sp-glass" style="display: grid; gap: 18px; align-content: start;">
        <div class="sp-card-header" style="margin-bottom: 0;">
            <h3 class="sp-card-title">
                <?= sp_icon('info', '', 18) ?>
                <span>Direct Channels</span>
            </h3>
        </div>
        <p style="color: var(--sp-text-secondary);">
            For urgent API integration assistance, enterprise volume plans, or security disclosures, contact our team directly.
        </p>

        <div class="sp-metric">
            <span class="sp-metric-label"><?= sp_icon('mail', '', 14) ?> Direct Support</span>
            <span class="sp-metric-value is-compact">support@scalepixel.io</span>
        </div>

        <div class="sp-metric">
            <span class="sp-metric-label"><?= sp_icon('code-2', '', 14) ?> Core Engineering</span>
            <span class="sp-metric-value is-compact">dev@scalepixel.io</span>
        </div>

        <div class="sp-alert" style="color: var(--sp-text-secondary); border-color: var(--sp-border-glass);">
            <?= sp_icon('book-open', '', 18) ?>
            <span>Looking for endpoint headers or SDK snippets? Check the <a href="docs.php">REST API Documentation</a>.</span>
        </div>
    </aside>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
