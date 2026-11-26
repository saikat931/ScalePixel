<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(24));
}
$pageTitle = 'ScalePixel - API Keys & Developer Dashboard';
$activeNav = 'keys';
require_once __DIR__ . '/includes/header.php';
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
?>

<div class="page-container" style="max-width: 1140px;">
    <!-- Dashboard Header -->
    <div style="margin-bottom: 32px; display: flex; align-items: flex-start; justify-content: space-between; flex-wrap: wrap; gap: 16px;">
        <div>
            <div class="hero-pill" style="margin-bottom: 8px;">ScalePixel Developer Infrastructure</div>
            <h1 style="font-size: 32px; font-weight: 900; letter-spacing: -0.5px;">ScalePixel API Keys & Access Tokens</h1>
            <p style="color: var(--text-secondary); font-size: 14px; max-width: 600px;">
                Generate and manage secure Source Key and Secret Key pairs for your backend servers, mobile applications, and automated pipelines.
            </p>
        </div>
        <a href="docs.php" class="nav-btn" style="text-decoration: none;">View API Docs ➔</a>
    </div>

    <!-- Alert for newly generated key -->
    <?php if ($createdKey): ?>
        <div class="glass-card" style="border-color: var(--emerald); background: rgba(0, 245, 160, 0.08); margin-bottom: 32px;">
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px;">
                <span style="font-size: 24px;">🎉</span>
                <div>
                    <h3 style="color: var(--emerald); font-size: 18px; font-weight: 800;">New ScalePixel API Key Generated!</h3>
                    <p style="font-size: 13px; color: var(--text-secondary);">Save your Secret Key securely in your environment variables. It acts as your application password.</p>
                </div>
            </div>
            <div style="background: rgba(0,0,0,0.5); padding: 16px; border-radius: var(--radius-sm); font-family: monospace; font-size: 13px;">
                <div style="margin-bottom: 8px;"><strong style="color: var(--cyan);">Source Key (Public):</strong> <?= htmlspecialchars($createdKey['source_key']) ?></div>
                <div><strong style="color: var(--emerald);">Secret Key (Private):</strong> <?= htmlspecialchars($createdKey['secret_key']) ?></div>
            </div>
        </div>
    <?php endif; ?>

    <!-- ScalePixel Key Generation & Active Keys Grid -->
    <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 24px; margin-bottom: 40px;">
        <!-- Generate Form -->
        <div class="glass-card">
            <h3 style="font-size: 18px; font-weight: 800; margin-bottom: 14px;">Generate ScalePixel Key</h3>
            <?php if ($errorMsg): ?>
                <div style="background: rgba(255, 94, 98, 0.15); border: 1px solid var(--coral); color: var(--coral); padding: 10px 14px; border-radius: var(--radius-sm); font-size: 13px; margin-bottom: 16px;">
                    <?= htmlspecialchars($errorMsg) ?>
                </div>
            <?php endif; ?>
            <form method="POST" action="api-dashboard.php">
                <input type="hidden" name="action" value="generate">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                <div style="margin-bottom: 16px;">
                    <label style="display: block; font-size: 12px; font-weight: 700; color: var(--text-secondary); margin-bottom: 6px;">APPLICATION NAME</label>
                    <input type="text" name="app_name" required placeholder="e.g. My Mobile App or Web Store" class="control-input">
                </div>
                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-size: 12px; font-weight: 700; color: var(--text-secondary); margin-bottom: 6px;">DEVELOPER PLAN</label>
                    <select name="plan" class="control-select">
                        <option value="Developer Starter (₹9 / month)" <?= $selectedPlan === 'starter_9' ? 'selected' : '' ?>>Developer Starter — ₹9 / month (10,000 requests)</option>
                        <option value="Pro Enterprise (₹49 / month)" <?= ($selectedPlan === 'pro_49' || empty($selectedPlan)) ? 'selected' : '' ?>>Pro Enterprise — ₹49 / month (100,000 requests)</option>
                        <option value="ScalePixel Dedicated (₹199 / month)" <?= $selectedPlan === 'dedicated' ? 'selected' : '' ?>>ScalePixel Dedicated Cloud — ₹199 / month (Unlimited)</option>
                    </select>
                </div>
                <button type="submit" class="nav-btn" style="width: 100%; border: none; cursor: pointer; padding: 12px;">Generate ScalePixel Key Pair</button>
            </form>
        </div>

        <!-- Active Keys Table -->
        <div class="glass-card">
            <h3 style="font-size: 18px; font-weight: 800; margin-bottom: 16px;">Active ScalePixel API Keys (<?= count($allKeys) ?>)</h3>
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                    <thead>
                        <tr style="border-bottom: 1px solid var(--border-subtle); color: var(--text-muted); text-align: left;">
                            <th style="padding: 10px 12px;">APPLICATION</th>
                            <th style="padding: 10px 12px;">SOURCE KEY</th>
                            <th style="padding: 10px 12px;">PLAN</th>
                            <th style="padding: 10px 12px;">USAGE</th>
                            <th style="padding: 10px 12px;">STATUS</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($allKeys as $pk => $info): ?>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.04);">
                                <td style="padding: 12px; font-weight: 700; color: #FFF;"><?= htmlspecialchars($info['name']) ?></td>
                                <td style="padding: 12px; font-family: monospace; color: var(--cyan);"><?= htmlspecialchars(substr($pk, 0, 18)) ?>...</td>
                                <td style="padding: 12px; color: var(--text-secondary);"><?= htmlspecialchars($info['plan']) ?></td>
                                <td style="padding: 12px; color: var(--emerald); font-weight: 700;"><?= intval($info['total_calls']) ?> calls</td>
                                <td style="padding: 12px;">
                                    <span style="background: rgba(0,245,160,0.15); color: var(--emerald); padding: 2px 8px; border-radius: var(--radius-full); font-size: 11px; font-weight: 800;">ACTIVE</span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
