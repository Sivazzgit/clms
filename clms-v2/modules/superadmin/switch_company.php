<?php
/**
 * CLMS 2.0 — Super Admin: Switch active company context
 * GET/POST /superadmin/switch-company?company_id=X
 *
 * Allows super_admin to "enter" a company so they see the correct tenant data
 * (and then optionally impersonate a user within it).
 */
Auth::requireRole('super_admin');

$companyId = (int)($_REQUEST['company_id'] ?? 0);

if (!$companyId) {
    Helpers::redirect('/superadmin', 'Please select a company.', 'error');
}

$company = DB::row('SELECT id, name, is_active FROM companies WHERE id = ?', [$companyId]);
if (!$company) {
    Helpers::redirect('/superadmin', 'Company not found.', 'error');
}

// Switch the super_admin's company_id context in session
// We DON'T start impersonation here — just update company_id so queries return that tenant's data.
// Real super_admin identity is preserved.
$_SESSION['company_id'] = $company['id'];

$_SESSION['flash_success'] = 'Context switched to ' . htmlspecialchars($company['name'], ENT_QUOTES) . '. You can now use the Users list to impersonate a user within this company.';
Helpers::redirect('/users');
