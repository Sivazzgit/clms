<?php
/**
 * CLMS 2.0 — base.html.php
 * Master page layout. Every module page wraps itself in this template.
 *
 * Usage:
 *   $pageTitle = 'Vendor List';
 *   $activeMenu = 'vendors';
 *   $breadcrumbs = [['label'=>'Contractors','url'=>'/vendors'], ['label'=>'List']];
 *   ob_start();
 *   // ... page content ...
 *   $pageContent = ob_get_clean();
 *   include CLMS_ROOT . '/templates/base.html.php';
 */

$user         = Auth::user();
$rolesConfig  = require CLMS_ROOT . '/config/roles.php';
$primaryRole  = Auth::primaryRole();
$menus        = $rolesConfig['menus'][$primaryRole] ?? [];
$pageTitle    = $pageTitle    ?? 'CLMS';
$breadcrumbs  = $breadcrumbs  ?? [];
$pageContent  = $pageContent  ?? '';
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="<?= htmlspecialchars(Auth::csrfToken()) ?>">
  <title><?= htmlspecialchars($pageTitle) ?> — CLMS</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= APP_BASE ?>/assets/css/theme.css">
  <link rel="stylesheet" href="<?= APP_BASE ?>/assets/css/layout.css">
  <link rel="stylesheet" href="<?= APP_BASE ?>/assets/css/components.css">
</head>
<body>

<div class="clms-shell">

  <!-- ============ SIDEBAR ============ -->
  <?php include __DIR__ . '/sidebar.html.php'; ?>

  <!-- ============ HEADER ============ -->
  <?php include __DIR__ . '/header.html.php'; ?>

  <!-- ============ MAIN CONTENT ============ -->

  <main class="clms-main" id="main-content">
    <?php if (Auth::isImpersonating()): ?>
    <div class="impersonation-banner">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
        <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
        <line x1="12" y1="9" x2="12" y2="13"/>
        <line x1="12" y1="17" x2="12.01" y2="17"/>
      </svg>
      <span>
        You are acting as
        <strong><?= Helpers::h($_SESSION['impersonating_as']['full_name'] ?? '') ?></strong>
        (<?= Helpers::h($_SESSION['impersonating_as']['username'] ?? '') ?>)
      </span>
      <?php
        $elapsed  = time() - ($_SESSION['impersonation_started_at'] ?? time());
        $minutes  = max(0, 60 - (int)($elapsed / 60));
      ?>
      <span class="impersonation-timer"><?= $minutes ?> min remaining</span>
      <form method="POST" action="/impersonate/exit" style="margin:0">
        <input type="hidden" name="<?= CSRF_KEY ?>" value="<?= Helpers::h(Auth::csrfToken()) ?>">
        <button type="submit" class="impersonation-exit-btn">Exit impersonation</button>
      </form>
    </div>
    <?php endif; ?>
    <?php echo $pageContent; ?>
  </main>

</div><!-- .clms-shell -->

<!-- Mobile overlay -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- Toast container (injected by JS) -->
<div id="toast-container"></div>

<!-- Server-side flash messages → JS toast -->
<?php if (!empty($_SESSION['flash_success'])): ?>
<script>document.addEventListener('DOMContentLoaded',()=>CLMS.toast.success(<?= json_encode($_SESSION['flash_success']) ?>));</script>
<?php unset($_SESSION['flash_success']); endif; ?>

<?php if (!empty($_SESSION['flash_error'])): ?>
<script>document.addEventListener('DOMContentLoaded',()=>CLMS.toast.error(<?= json_encode($_SESSION['flash_error']) ?>));</script>
<?php unset($_SESSION['flash_error']); endif; ?>

<!-- Scripts — order matters -->
<script src="<?= APP_BASE ?>/assets/js/utils.js"></script>
<script src="<?= APP_BASE ?>/assets/js/api.js"></script>
<script src="<?= APP_BASE ?>/assets/js/forms.js"></script>
<script src="<?= APP_BASE ?>/assets/js/tables.js"></script>
<script src="<?= APP_BASE ?>/assets/js/app.js"></script>

<?php if (!empty($pageScripts)): ?>
  <?php foreach ($pageScripts as $script): ?>
    <script src="<?= htmlspecialchars($script) ?>"></script>
  <?php endforeach; ?>
<?php endif; ?>

<?php if (!empty($inlineScript)): ?>
<script><?= $inlineScript ?></script>
<?php endif; ?>

</body>
</html>
