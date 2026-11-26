<?php
$pageTitle = 'Contact Support & Enterprise Inquiries - ScalePixel';
$activeNav = 'contact';
require_once __DIR__ . '/includes/header.php';

$sentSuccess = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'])) {
    $sentSuccess = true;
}
?>

<div class="page-container" style="max-width: 800px;">
    <div class="legal-card">
        <h1>Contact & Developer Support</h1>
        <div class="meta-date">Technical Support • Enterprise Inquiries • SLA Assistance</div>

        <?php if ($sentSuccess): ?>
            <div style="background: rgba(0, 245, 160, 0.12); border: 1px solid var(--emerald); padding: 16px; border-radius: var(--radius-sm); margin-bottom: 24px; color: var(--emerald); font-weight: 700;">
                ✓ Thank you! Your inquiry has been received. Our engineering team typically responds within 2 business hours.
            </div>
        <?php endif; ?>

        <p style="margin-bottom: 24px;">
            Have questions about custom rate limits, AWS Lambda deployment, or integrating ScalePixel with your mobile applications? Send a message to our engineering team.
        </p>

        <form method="POST" action="contact.php">
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 12px; font-weight: 700; color: var(--text-secondary); margin-bottom: 6px;">YOUR NAME</label>
                <input type="text" name="name" required placeholder="e.g. John Doe" class="control-input">
            </div>

            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 12px; font-weight: 700; color: var(--text-secondary); margin-bottom: 6px;">EMAIL ADDRESS</label>
                <input type="email" name="email" required placeholder="e.g. developer@company.com" class="control-input">
            </div>

            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 12px; font-weight: 700; color: var(--text-secondary); margin-bottom: 6px;">SUBJECT / INQUIRY TYPE</label>
                <select name="subject" class="control-select">
                    <option value="Integration">Platform & Mobile App SDK Integration</option>
                    <option value="API Keys">API Keys & Quota Limit Increase</option>
                    <option value="AWS Lambda">AWS Lambda / GitHub Auto-Deploy</option>
                    <option value="Bug Report">Technical Bug Report</option>
                    <option value="General">General Inquiry</option>
                </select>
            </div>

            <div style="margin-bottom: 24px;">
                <label style="display: block; font-size: 12px; font-weight: 700; color: var(--text-secondary); margin-bottom: 6px;">MESSAGE</label>
                <textarea name="message" required rows="5" placeholder="How can we assist your integration?" class="control-input" style="resize: vertical;"></textarea>
            </div>

            <button type="submit" class="nav-btn" style="width: 100%; border: none; cursor: pointer; padding: 14px; font-size: 14px;">
                Send Message ➔
            </button>
        </form>

        <div style="margin-top: 36px; padding-top: 24px; border-top: 1px solid var(--border-subtle); display: flex; justify-content: space-between; flex-wrap: wrap; gap: 16px; font-size: 13px; color: var(--text-secondary);">
            <div>📧 Direct Support: <strong style="color: var(--cyan);">support@scalepixel.io</strong></div>
            <div>⚡ ScalePixel Core Team: <strong style="color: var(--emerald);">dev@scalepixel.io</strong></div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
