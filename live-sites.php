<?php
require_once __DIR__ . '/includes/icons.php';
require_once __DIR__ . '/includes/LiveSites.php';

ScalePixelLiveSites::startSecureSession();

$action = $_GET['action'] ?? $_POST['action'] ?? '';

// JSON endpoints for authenticated admin polling and auto-clear on close
if ($action === 'list') {
    header('Content-Type: application/json');
    header('Cache-Control: no-store, no-cache, must-revalidate');
    if (!ScalePixelLiveSites::isAuthenticated()) {
        http_response_code(401);
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
        exit;
    }
    $sites = ScalePixelLiveSites::getSites();
    echo json_encode([
        'status' => 'success',
        'count'  => count($sites),
        'sites'  => $sites,
    ], JSON_UNESCAPED_SLASHES);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'clear') {
    $isAjax = (isset($_GET['ajax']) || (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false));
    if (!ScalePixelLiveSites::isAuthenticated() || !ScalePixelLiveSites::verifyCsrf($_POST['csrf_token'] ?? null)) {
        if ($isAjax) {
            http_response_code(403);
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Unauthorized or invalid CSRF token.']);
            exit;
        }
        $authError = 'Security validation failed.';
    } else {
        ScalePixelLiveSites::clear();
        if ($isAjax || $action === 'clear' && isset($_GET['beacon'])) {
            http_response_code(204);
            exit;
        }
        $statusNotice = 'Website log cleared.';
    }
}

$authError = $authError ?? '';
$statusNotice = $statusNotice ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'setup') {
    if (!ScalePixelLiveSites::verifyCsrf($_POST['csrf_token'] ?? null)) {
        $authError = 'Security validation failed: invalid CSRF token.';
    } else {
        $pass = (string)($_POST['password'] ?? '');
        $confirm = (string)($_POST['password_confirm'] ?? '');
        if ($pass !== $confirm) {
            $authError = 'Passwords do not match.';
        } else {
            $res = ScalePixelLiveSites::setupInitialAdmin($pass);
            if ($res['ok']) {
                ScalePixelLiveSites::attemptLogin($pass);
                header('Location: live-sites.php');
                exit;
            }
            $authError = $res['error'];
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'login') {
    if (!ScalePixelLiveSites::verifyCsrf($_POST['csrf_token'] ?? null)) {
        $authError = 'Security validation failed: invalid CSRF token.';
    } else {
        $res = ScalePixelLiveSites::attemptLogin((string)($_POST['password'] ?? ''));
        if ($res['ok']) {
            header('Location: live-sites.php');
            exit;
        }
        $authError = $res['error'];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'logout') {
    if (ScalePixelLiveSites::verifyCsrf($_POST['csrf_token'] ?? null)) {
        ScalePixelLiveSites::logout();
    }
    header('Location: live-sites.php');
    exit;
}

$isConfigured = ScalePixelLiveSites::isAdminConfigured();
$isAuthed = ScalePixelLiveSites::isAuthenticated();
$sites = $isAuthed ? ScalePixelLiveSites::getSites() : [];
$csrfToken = $_SESSION['sp_admin_csrf'];

$pageTitle = 'ScalePixel - Live Client Websites';
$activeNav = 'live-sites';
$pageEyebrow = 'Protected Ephemeral Monitor';
$pageHeading = 'Live client websites';
$pageSubtitle = 'Shows only website URLs currently calling ScalePixel. The log clears automatically when you close this page.';
$headerActionsHtml = $isAuthed
    ? '<form method="POST" action="live-sites.php" style="display:inline;">'
      . '<input type="hidden" name="action" value="logout">'
      . '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') . '">'
      . '<button type="submit" class="sp-btn">' . sp_icon('log-out', '', 16) . '<span>Sign Out</span></button>'
      . '</form>'
    : '';

require_once __DIR__ . '/includes/header.php';
?>

<?php if (!$isConfigured): ?>
    <section class="sp-glass" style="max-width: 520px;">
        <div class="sp-card-header">
            <h2 class="sp-card-title" style="font-size: 20px;">
                <?= sp_icon('lock', '', 18) ?>
                <span>Set Admin Password</span>
            </h2>
        </div>
        <p style="color: var(--sp-text-secondary); margin-bottom: 18px;">
            Create an admin password to protect your live website URL monitor. Stored locally as a salted hash with zero database.
        </p>

        <?php if ($authError): ?>
            <div class="sp-alert sp-state-error" role="alert" style="margin-bottom: 16px;">
                <?= sp_icon('alert-circle', '', 18) ?>
                <span><?= htmlspecialchars($authError, ENT_QUOTES, 'UTF-8') ?></span>
            </div>
        <?php endif; ?>

        <form method="POST" action="live-sites.php" class="sp-form-stack">
            <input type="hidden" name="action" value="setup">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">

            <div class="sp-field">
                <label class="sp-label" for="setup_password">Admin Password (min 8 characters)</label>
                <input type="password" id="setup_password" name="password" minlength="8" required autocomplete="new-password" class="sp-input">
            </div>

            <div class="sp-field">
                <label class="sp-label" for="setup_confirm">Confirm Password</label>
                <input type="password" id="setup_confirm" name="password_confirm" minlength="8" required autocomplete="new-password" class="sp-input">
            </div>

            <button type="submit" class="sp-btn sp-btn-primary" style="width: 100%;">
                <?= sp_icon('shield-check', '', 16) ?>
                <span>Save Password &amp; Open Monitor</span>
            </button>
        </form>
    </section>

<?php elseif (!$isAuthed): ?>
    <section class="sp-glass" style="max-width: 480px;">
        <div class="sp-card-header">
            <h2 class="sp-card-title" style="font-size: 20px;">
                <?= sp_icon('lock', '', 18) ?>
                <span>Admin Authentication Required</span>
            </h2>
        </div>
        <p style="color: var(--sp-text-secondary); margin-bottom: 18px;">
            Enter your admin password to view websites calling ScalePixel.
        </p>

        <?php if ($authError): ?>
            <div class="sp-alert sp-state-error" role="alert" style="margin-bottom: 16px;">
                <?= sp_icon('alert-circle', '', 18) ?>
                <span><?= htmlspecialchars($authError, ENT_QUOTES, 'UTF-8') ?></span>
            </div>
        <?php endif; ?>

        <form method="POST" action="live-sites.php" class="sp-form-stack">
            <input type="hidden" name="action" value="login">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">

            <div class="sp-field">
                <label class="sp-label" for="admin_password">Admin Password</label>
                <input type="password" id="admin_password" name="password" required autocomplete="current-password" class="sp-input">
            </div>

            <button type="submit" class="sp-btn sp-btn-primary" style="width: 100%;">
                <?= sp_icon('lock', '', 16) ?>
                <span>Unlock Live Sites</span>
            </button>
        </form>
    </section>

<?php else: ?>
    <?php if ($statusNotice): ?>
        <div class="sp-alert sp-state-success" role="status">
            <?= sp_icon('check-circle-2', '', 18) ?>
            <span><?= htmlspecialchars($statusNotice, ENT_QUOTES, 'UTF-8') ?></span>
        </div>
    <?php endif; ?>

    <section class="sp-metrics" aria-label="Live websites summary">
        <div class="sp-metric">
            <span class="sp-metric-label"><?= sp_icon('globe', '', 14) ?> Active Websites</span>
            <span class="sp-metric-value" id="live-sites-count"><?= count($sites) ?></span>
        </div>
        <div class="sp-metric">
            <span class="sp-metric-label"><?= sp_icon('shield-check', '', 14) ?> Stored Fields</span>
            <span class="sp-metric-value is-compact">URL Only</span>
        </div>
        <div class="sp-metric">
            <span class="sp-metric-label"><?= sp_icon('trash-2', '', 14) ?> Auto-Purge</span>
            <span class="sp-metric-value is-compact">Clears on page close</span>
        </div>
    </section>

    <section class="sp-glass" aria-label="Website URL list">
        <div class="sp-card-header">
            <h2 class="sp-card-title" style="font-size: 20px;">
                <?= sp_icon('globe', '', 18) ?>
                <span>Websites Using ScalePixel</span>
            </h2>
            <form method="POST" action="live-sites.php" id="clear-sites-form">
                <input type="hidden" name="action" value="clear">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                <button type="submit" class="sp-btn sp-btn-sm" id="clear-sites-btn" <?= empty($sites) ? 'disabled' : '' ?>>
                    <?= sp_icon('trash-2', '', 15) ?>
                    <span>Clear Now</span>
                </button>
            </form>
        </div>

        <p class="sp-helper" style="margin-bottom: 16px;">
            Live polling updates every 3 seconds while this tab is open. Closing or leaving this page automatically empties the URL log file.
        </p>

        <div class="sp-empty" id="live-sites-empty" <?= !empty($sites) ? 'hidden' : '' ?>>
            <?= sp_icon('globe', '', 28) ?>
            <p style="font-weight: 500; color: var(--sp-text); margin-bottom: 4px;">No website URLs logged yet</p>
            <p class="sp-helper">External websites calling <code>/api/v1/compress.php</code> or <code>/api/v1/convert.php</code> will appear here.</p>
        </div>

        <div class="sp-table-wrap" id="live-sites-table-wrap" <?= empty($sites) ? 'hidden' : '' ?>>
            <table class="sp-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>WEBSITE URL</th>
                        <th style="text-align: right;">ACTIONS</th>
                    </tr>
                </thead>
                <tbody id="live-sites-tbody">
                    <?php foreach ($sites as $idx => $url): ?>
                        <tr>
                            <td style="width: 48px; color: var(--sp-text-muted);"><?= $idx + 1 ?></td>
                            <td>
                                <a href="<?= htmlspecialchars($url, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener noreferrer nofollow">
                                    <?= htmlspecialchars($url, ENT_QUOTES, 'UTF-8') ?>
                                </a>
                            </td>
                            <td style="text-align: right;">
                                <button type="button" class="sp-btn sp-btn-ghost sp-btn-sm" data-copy="<?= htmlspecialchars($url, ENT_QUOTES, 'UTF-8') ?>">
                                    <?= sp_icon('copy', '', 14) ?>
                                    <span>Copy</span>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>

    <script>
      (function () {
        const csrfToken = <?= json_encode($csrfToken) ?>;
        const countEl = document.getElementById('live-sites-count');
        const emptyEl = document.getElementById('live-sites-empty');
        const wrapEl = document.getElementById('live-sites-table-wrap');
        const tbodyEl = document.getElementById('live-sites-tbody');
        const clearBtn = document.getElementById('clear-sites-btn');

        function escapeHtml(str) {
          const d = document.createElement('div');
          d.textContent = String(str);
          return d.innerHTML;
        }

        function renderSites(sites) {
          if (countEl) countEl.textContent = String(sites.length);
          if (clearBtn) clearBtn.disabled = sites.length === 0;
          if (!sites.length) {
            if (emptyEl) emptyEl.hidden = false;
            if (wrapEl) wrapEl.hidden = true;
            if (tbodyEl) tbodyEl.innerHTML = '';
            return;
          }
          if (emptyEl) emptyEl.hidden = true;
          if (wrapEl) wrapEl.hidden = false;
          if (tbodyEl) {
            tbodyEl.innerHTML = sites.map((url, idx) => {
              const safeUrl = escapeHtml(url);
              return `
                <tr>
                  <td style="width:48px;color:var(--sp-text-muted);">${idx + 1}</td>
                  <td><a href="${safeUrl}" target="_blank" rel="noopener noreferrer nofollow">${safeUrl}</a></td>
                  <td style="text-align:right;">
                    <button type="button" class="sp-btn sp-btn-ghost sp-btn-sm" data-copy="${safeUrl}">
                      <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="sp-icon" aria-hidden="true"><rect width="14" height="14" x="8" y="8" rx="2" ry="2"></rect><path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"></path></svg>
                      <span>Copy</span>
                    </button>
                  </td>
                </tr>
              `;
            }).join('');
          }
        }

        const pollTimer = setInterval(async () => {
          if (document.hidden) return;
          try {
            const res = await fetch('live-sites.php?action=list', { cache: 'no-store' });
            if (!res.ok) return;
            const data = await res.json();
            if (data.status === 'success' && Array.isArray(data.sites)) {
              renderSites(data.sites);
            }
          } catch {
            // Ignore transient polling failures
          }
        }, 3000);

        let clearedOnExit = false;
        function clearOnClose() {
          if (clearedOnExit) return;
          clearedOnExit = true;
          clearInterval(pollTimer);
          const payload = new FormData();
          payload.append('action', 'clear');
          payload.append('csrf_token', csrfToken);
          navigator.sendBeacon('live-sites.php?action=clear&beacon=1', payload);
        }

        window.addEventListener('pagehide', clearOnClose);
      })();
    </script>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
