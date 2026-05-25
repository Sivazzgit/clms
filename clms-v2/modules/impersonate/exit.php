<?php
/**
 * CLMS 2.0 — End Impersonation
 * POST /impersonate/exit
 */
Auth::requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    Helpers::redirect('/dashboard');
}

Auth::requireCsrf();

if (!Auth::isImpersonating()) {
    Helpers::redirect('/dashboard', 'No active impersonation session.', 'info');
}

$impersonatedName = $_SESSION['impersonating_as']['full_name'] ?? 'that user';
Auth::endImpersonation();

$_SESSION['flash_success'] = 'Returned to your own account. (Was acting as ' . htmlspecialchars($impersonatedName, ENT_QUOTES) . ')';
Helpers::redirect('/impersonate');
