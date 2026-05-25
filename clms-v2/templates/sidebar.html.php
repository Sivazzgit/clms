<?php
/**
 * CLMS 2.0 — sidebar.html.php
 * Role-aware left navigation. Included by base.html.php.
 * Variables available: $menus, $user, $primaryRole, $activeMenu
 */

$activeMenu = $activeMenu ?? '';

function renderSidebarItem(array $item, string $activeMenu, int $depth = 0): void
{
    $hasSubmenu = !empty($item['submenu']);
    $base       = defined('APP_BASE') ? APP_BASE : '';
    $rawUrl     = $item['url'];
    // Prefix absolute internal paths with APP_BASE (skip # anchors and external URLs)
    $resolvedUrl = ($rawUrl !== '#' && str_starts_with($rawUrl, '/')) ? $base . $rawUrl : $rawUrl;
    $url        = htmlspecialchars($resolvedUrl);
    $label      = htmlspecialchars($item['label']);
    $icon       = $item['icon'] ?? 'circle';

    $isActive = $activeMenu === ($item['key'] ?? '');

    echo '<li class="sidebar-item' . ($isActive ? ' open' : '') . '">';

    if ($hasSubmenu) {
        echo '<a class="sidebar-link' . ($isActive ? ' active' : '') . '" href="#" data-submenu="1">';
    } else {
        echo '<a class="sidebar-link' . ($isActive ? ' active' : '') . '" href="' . $url . '">';
    }

    // Icon (inline SVG via data-icon attribute — rendered by app.js icon sprite)
    echo '<svg class="nav-icon" data-icon="' . htmlspecialchars($icon) . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">';
    echo renderSvgIcon($icon);
    echo '</svg>';

    echo '<span class="nav-label">' . $label . '</span>';

    if ($hasSubmenu) {
        echo '<svg class="nav-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>';
    }

    echo '</a>';

    if ($hasSubmenu) {
        echo '<ul class="sidebar-submenu">';
        foreach ($item['submenu'] as $sub) {
            echo '<li class="sidebar-item">';
            $subActive = ($activeMenu === ($sub['key'] ?? '')) ? ' active' : '';
            $subRaw    = $sub['url'];
            $subUrl    = ($subRaw !== '#' && str_starts_with($subRaw, '/')) ? $base . $subRaw : $subRaw;
            echo '<a class="sidebar-link' . $subActive . '" href="' . htmlspecialchars($subUrl) . '">';
            echo '<span class="nav-label">' . htmlspecialchars($sub['label']) . '</span>';
            echo '</a>';
            echo '</li>';
        }
        echo '</ul>';
    }

    echo '</li>';
}

// Minimal inline SVG paths for nav icons
function renderSvgIcon(string $name): string
{
    $icons = [
        'home'           => '<path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>',
        'settings'       => '<circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/>',
        'users'          => '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>',
        'briefcase'      => '<rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>',
        'id-card'        => '<rect x="2" y="5" width="20" height="14" rx="2"/><circle cx="8.5" cy="12" r="2.5"/><path d="M14 9h4M14 12h4M14 15h2"/>',
        'clipboard'      => '<path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><rect x="8" y="2" width="8" height="4" rx="1" ry="1"/>',
        'users-check'    => '<path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><polyline points="17 11 19 13 23 9"/>',
        'calendar-check' => '<rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/><polyline points="9 16 11 18 15 14"/>',
        'file-invoice'   => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/>',
        'landmark'       => '<line x1="3" y1="22" x2="21" y2="22"/><line x1="6" y1="18" x2="6" y2="11"/><line x1="10" y1="18" x2="10" y2="11"/><line x1="14" y1="18" x2="14" y2="11"/><line x1="18" y1="18" x2="18" y2="11"/><polygon points="12 2 20 7 4 7"/>',
        'bar-chart'      => '<line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/><line x1="2" y1="20" x2="22" y2="20"/>',
        'shield'         => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>',
        'eye'            => '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>',
        'circle'         => '<circle cx="12" cy="12" r="10"/>',
    ];
    return $icons[$name] ?? $icons['circle'];
}
?>
<aside class="clms-sidebar" id="sidebar" aria-label="Main navigation">

  <!-- Brand -->
  <a href="<?= defined('APP_BASE') ? APP_BASE : '' ?>/dashboard" class="sidebar-brand">
    <div class="sidebar-brand-logo">C</div>
    <div class="sidebar-brand-text">
      <span class="sidebar-brand-name">CLMS 2.0</span>
      <span class="sidebar-brand-sub">Contract Labour Mgmt</span>
    </div>
  </a>

  <!-- Navigation -->
  <ul class="sidebar-nav" role="navigation">
    <?php foreach ($menus as $item): ?>
      <?php renderSidebarItem($item, $activeMenu); ?>
    <?php endforeach; ?>
  </ul>

  <!-- User card at bottom -->
  <div class="sidebar-user">
    <div class="sidebar-user-avatar">
      <?= htmlspecialchars(mb_strtoupper(mb_substr($user['full_name'] ?? 'U', 0, 1))) ?>
    </div>
    <div class="sidebar-user-info">
      <div class="sidebar-user-name"><?= htmlspecialchars($user['full_name'] ?? '') ?></div>
      <div class="sidebar-user-role"><?= htmlspecialchars(ucwords(str_replace('_', ' ', $primaryRole))) ?></div>
    </div>
    <a href="<?= defined('APP_BASE') ? APP_BASE : '' ?>/logout" title="Logout" style="color:rgba(255,255,255,.55);flex-shrink:0;">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
        <polyline points="16 17 21 12 16 7"/>
        <line x1="21" y1="12" x2="9" y2="12"/>
      </svg>
    </a>
  </div>

</aside>
