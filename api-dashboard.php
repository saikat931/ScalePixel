<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(24));
}

require_once __DIR__ . '/includes/icons.php';
require_once __DIR__ . '/includes/Auth.php';

$createdKey = null;
$errorMsg = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'generate') {
    $submittedCsrf = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'], $submittedCsrf)) {
        $errorMsg = 'Security validation failed: Invalid or expired CSRF token.';
    } else {
        $appName = trim($_POST['app_name'] ?? 'ScalePixel App');
        $plan = trim($_POST['plan'] ?? 'Developer Starter (₹9 / month)');
        $createdKey = ScalePixelAuth::generateKeyPair($appName, $plan);
    }
}

$allKeys = ScalePixelAuth::getKeys();
$selectedPlan = $_GET['plan'] ?? 'pro_49';

$totalCalls = 0;
$totalBytesSaved = 0;
foreach ($allKeys as $info) {
    $totalCalls += (int)($info['total_calls'] ?? 0);
    $totalBytesSaved += (int)($info['bytes_saved'] ?? 0);
}
$totalMbSaved = round($totalBytesSaved / (1024 * 1024), 2);

$pageTitle = 'ScalePixel - API Keys & Developer Dashboard';
$activeNav = 'keys';
$pageEyebrow = 'Developer Infrastructure';
$pageHeading = 'API keys & access tokens';
$pageSubtitle = 'Generate and manage Source Key and Secret Key pairs for backend servers, mobile apps, and automated pipelines.';
$headerActionsHtml = '<a href="docs.php" class="sp-btn">' . sp_icon('book-open', '', 16) . '<span>View API Docs</span></a>';

require_once __DIR__ . '/includes/header.php';
?>

<!-- Real Usage Summary -->
<section class="sp-metrics" aria-label="API usage summary">
    <div class="sp-metric">
        <span class="sp-metric-label"><?= sp_icon('key', '', 14) ?> Active Keys</span>
        <span class="sp-metric-value"><?= count($allKeys) ?></span>
    </div>
    <div class="sp-metric">
        <span class="sp-metric-label"><?= sp_icon('zap', '', 14) ?> Total API Calls</span>
        <span class="sp-metric-value"><?= number_format($totalCalls) ?></span>
    </div>
    <div class="sp-metric">
        <span class="sp-metric-label"><?= sp_icon('hard-drive', '', 14) ?> Bandwidth Saved</span>
        <span class="sp-metric-value"><?= number_format($totalMbSaved, 2) ?> MB</span>
    </div>
</section>

<!-- Alert for newly generated key -->
<?php if ($createdKey): ?>
    <section class="sp-glass" style="border-color: rgba(169, 237, 69, 0.45);" aria-live="polite">
        <div class="sp-card-header" style="margin-bottom: 12px;">
            <h3 class="sp-card-title sp-state-success">
                <?= sp_icon('check-circle-2', 'sp-state-success', 20) ?>
                <span>New ScalePixel API key generated</span>
            </h3>
            <span class="sp-badge sp-badge-success">Save Secret Now</span>
        </div>
        <p style="color: var(--sp-text-secondary); margin-bottom: 14px;">
            Store your Secret Key in environment variables. It acts as your application credential.
        </p>

        <div class="sp-grid-2">
            <div class="sp-field">
                <span class="sp-label">Source Key (Public)</span>
                <div style="display: flex; gap: 8px; align-items: center;">
                    <input type="text" readonly class="sp-input" value="<?= htmlspecialchars($createdKey['source_key'], ENT_QUOTES, 'UTF-8') ?>" aria-label="Generated Source Key">
                    <button type="button" class="sp-btn sp-btn-sm" data-copy="<?= htmlspecialchars($createdKey['source_key'], ENT_QUOTES, 'UTF-8') ?>" aria-label="Copy Source Key">
                        <?= sp_icon('copy', '', 15) ?>
                        <span>Copy</span>
                    </button>
                </div>
            </div>

            <div class="sp-field">
                <span class="sp-label">Secret Key (Private)</span>
                <div style="display: flex; gap: 8px; align-items: center;">
                    <input type="text" readonly class="sp-input" value="<?= htmlspecialchars($createdKey['secret_key'], ENT_QUOTES, 'UTF-8') ?>" aria-label="Generated Secret Key">
                    <button type="button" class="sp-btn sp-btn-primary sp-btn-sm" data-copy="<?= htmlspecialchars($createdKey['secret_key'], ENT_QUOTES, 'UTF-8') ?>" aria-label="Copy Secret Key">
                        <?= sp_icon('copy', '', 15) ?>
                        <span>Copy</span>
                    </button>
                </div>
            </div>
        </div>
    </section>
<?php endif; ?>

<!-- Key Generation & Active Keys Grid -->
<section class="sp-dashboard-grid" aria-label="Key generation and active credentials">
    <div class="sp-glass">
        <div class="sp-card-header">
            <h3 class="sp-card-title">
                <?= sp_icon('plus', '', 18) ?>
                <span>Generate API Key</span>
            </h3>
        </div>

        <?php if ($errorMsg): ?>
            <div class="sp-alert sp-state-error" role="alert" style="margin-bottom: 16px;">
                <?= sp_icon('alert-circle', '', 18) ?>
                <span><?= htmlspecialchars($errorMsg, ENT_QUOTES, 'UTF-8') ?></span>
            </div>
        <?php endif; ?>

        <form method="POST" action="api-dashboard.php" class="sp-form-stack">
            <input type="hidden" name="action" value="generate">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">

            <div class="sp-field">
                <label class="sp-label" for="app_name">Application Name</label>
                <input
                    type="text"
                    id="app_name"
                    name="app_name"
                    required
                    placeholder="e.g. My Mobile App or Web Store"
                    class="sp-input"
                >
            </div>

            <div class="sp-field">
                <label class="sp-label" for="plan">Developer Plan</label>
                <select id="plan" name="plan" class="sp-select">
                    <option value="Developer Starter (₹9 / month)" <?= $selectedPlan === 'starter_9' ? 'selected' : '' ?>>Developer Starter — &#8377;9 / month (10,000 requests)</option>
                    <option value="Pro Enterprise (₹49 / month)" <?= ($selectedPlan === 'pro_49' || empty($selectedPlan)) ? 'selected' : '' ?>>Pro Enterprise — &#8377;49 / month (100,000 requests)</option>
                    <option value="ScalePixel Dedicated (₹199 / month)" <?= $selectedPlan === 'dedicated' ? 'selected' : '' ?>>ScalePixel Dedicated Cloud — &#8377;199 / month (Unlimited)</option>
                </select>
            </div>

            <button type="submit" class="sp-btn sp-btn-primary" style="width: 100%;">
                <?= sp_icon('key', '', 16) ?>
                <span>Generate Key Pair</span>
            </button>
        </form>
    </div>

    <div class="sp-glass">
        <div class="sp-card-header">
            <h3 class="sp-card-title">
                <?= sp_icon('shield-check', '', 18) ?>
                <span>Active ScalePixel API Keys (<?= count($allKeys) ?>)</span>
            </h3>
        </div>

        <?php if (empty($allKeys)): ?>
            <div class="sp-empty">
                <?= sp_icon('key', '', 28) ?>
                <p style="font-weight: 500; color: var(--sp-text); margin-bottom: 4px;">No API keys created yet</p>
                <p class="sp-helper">Generate a Source Key and Secret Key pair on the left to authenticate REST API requests.</p>
            </div>
        <?php else: ?>
            <div class="sp-table-wrap">
                <table class="sp-table">
                    <thead>
                        <tr>
                            <th>APPLICATION</th>
                            <th>SOURCE KEY</th>
                            <th>PLAN</th>
                            <th>USAGE</th>
                            <th>STATUS</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($allKeys as $pk => $info): ?>
                            <tr>
                                <td style="font-weight: 600; color: var(--sp-text);"><?= htmlspecialchars($info['name'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <code><?= htmlspecialchars(substr($pk, 0, 18), ENT_QUOTES, 'UTF-8') ?>...</code>
                                        <button
                                            type="button"
                                            class="sp-btn sp-btn-ghost sp-btn-sm"
                                            data-copy="<?= htmlspecialchars($pk, ENT_QUOTES, 'UTF-8') ?>"
                                            aria-label="Copy source key for <?= htmlspecialchars($info['name'], ENT_QUOTES, 'UTF-8') ?>"
                                            title="Copy full Source Key"
                                        >
                                            <?= sp_icon('copy', '', 14) ?>
                                        </button>
                                    </div>
                                </td>
                                <td><?= htmlspecialchars($info['plan'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td class="sp-state-success" style="font-weight: 600; font-variant-numeric: tabular-nums;"><?= intval($info['total_calls']) ?> calls</td>
                                <td>
                                    <?php if (!empty($info['active'])): ?>
                                        <span class="sp-badge sp-badge-success">ACTIVE</span>
                                    <?php else: ?>
                                        <span class="sp-badge">INACTIVE</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
