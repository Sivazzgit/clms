<?php
/**
 * CLMS 2.0 — Billing Actions (POST handler)
 */
Auth::requireRole(['hr_admin','finance']);
if ($_SERVER['REQUEST_METHOD'] !== 'POST') Helpers::redirect('/billing');
Auth::requireCsrf();

$companyId = $_SESSION['company_id'];
$billingId = (int)($_POST['billing_id'] ?? 0);
$action    = Helpers::clean($_POST['action'] ?? '');

$period = DB::row("SELECT * FROM billing_periods WHERE id=? AND company_id=?", [$billingId,$companyId]);
if (!$period) Helpers::redirect('/billing', 'Not found.', 'error');

switch ($action) {
    case 'submit':
        if ($period['status'] !== 'draft') Helpers::redirect("/billing/$billingId/view", 'Already submitted.', 'error');
        DB::execute("UPDATE billing_periods SET status='submitted',updated_at=NOW() WHERE id=?", [$billingId]);
        AuditLogger::log('SUBMIT','billing',$billingId);
        Helpers::redirect("/billing/$billingId/view", 'Bill submitted for approval.', 'success');
        break;
    case 'approve':
        if ($period['status'] !== 'submitted') Helpers::redirect("/billing/$billingId/view", 'Invalid state.', 'error');
        DB::execute("UPDATE billing_periods SET status='approved',updated_at=NOW() WHERE id=?", [$billingId]);
        AuditLogger::log('APPROVE','billing',$billingId);
        Helpers::redirect("/billing/$billingId/view", 'Bill approved.', 'success');
        break;
    default:
        Helpers::redirect("/billing/$billingId/view", 'Unknown action.', 'error');
}
