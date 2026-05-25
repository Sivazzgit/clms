<?php
/**
 * CLMS 2.0 — header.html.php
 * Top application header. Included by base.html.php.
 * Variables: $pageTitle, $breadcrumbs
 */
?>
<header class="clms-header" role="banner">

  <!-- Mobile sidebar toggle -->
  <button class="header-menu-btn" id="sidebarToggle" aria-label="Toggle navigation" aria-expanded="false">
    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
      <line x1="3" y1="6" x2="21" y2="6"/>
      <line x1="3" y1="12" x2="21" y2="12"/>
      <line x1="3" y1="18" x2="21" y2="18"/>
    </svg>
  </button>

  <!-- Breadcrumb -->
  <nav class="header-breadcrumb" aria-label="Breadcrumb">
    <?php if (!empty($breadcrumbs)): ?>
      <?php foreach ($breadcrumbs as $i => $crumb): ?>
        <?php if ($i < count($breadcrumbs) - 1): ?>
          <a href="<?= htmlspecialchars($crumb['url'] ?? '#') ?>"><?= htmlspecialchars($crumb['label']) ?></a>
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <polyline points="9 18 15 12 9 6"/>
          </svg>
        <?php else: ?>
          <span aria-current="page"><?= htmlspecialchars($crumb['label']) ?></span>
        <?php endif; ?>
      <?php endforeach; ?>
    <?php else: ?>
      <span><?= htmlspecialchars($pageTitle) ?></span>
    <?php endif; ?>
  </nav>

  <!-- Right side: user info + quick actions -->
  <div class="header-right">
    <span class="header-company">
      <?php
        $company = DB::row('SELECT name FROM companies WHERE id = ? LIMIT 1', [$_SESSION['company_id'] ?? 1]);
        echo htmlspecialchars($company['name'] ?? '');
      ?>
    </span>
    <div class="header-user-avatar">
      <?= htmlspecialchars(mb_strtoupper(mb_substr($user['full_name'] ?? 'U', 0, 1))) ?>
    </div>
  </div>

</header>
