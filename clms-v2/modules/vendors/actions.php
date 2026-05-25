<?php
/**
 * CLMS 2.0 — Vendor POST Actions (delete, approve, suspend)
 * All via POST AJAX from the vendor list
 */
Auth::requireRole('hr_admin');
Auth::requireCsrf();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    Helpers::jsonFail('Invalid request.');
}

$action = Helpers::clean($_POST['action'] ?? '');
$id     = (int)($_POST['id'] ?? 0);

if (!$id) Helpers::jsonFail('Invalid vendor.');

$vendor = DB::row('SELECT * FROM vendors WHERE id = ? AND company_id = ?', [$id, $_SESSION['company_id']]);
if (!$vendor) Helpers::jsonFail('Contractor not found.');

switch ($action) {
    // ----------------------------------------------------------------
    case 'delete':
        $empCount = (int) DB::value('SELECT COUNT(*) FROM employees WHERE vendor_id = ?', [$id]);
        if ($empCount > 0) {
            Helpers::jsonFail("Cannot delete: this contractor has $empCount employee(s) on record.");
        }
        // Delete documents first
        $docs = DB::rows('SELECT file_path FROM vendor_documents WHERE vendor_id = ?', [$id]);
        foreach ($docs as $doc) {
            $path = CLMS_ROOT . '/' . ltrim($doc['file_path'], '/');
            if (file_exists($path)) @unlink($path);
        }
        DB::delete('vendor_documents', ['vendor_id' => $id]);
        DB::delete('vendors', ['id' => $id]);
        AuditLogger::log('DELETE', 'vendors', null, (string)$id, AuditLogger::sanitize($vendor), null);
        Helpers::jsonOk('Contractor deleted.');
        break;

    // ----------------------------------------------------------------
    case 'approve':
        DB::update('vendors', [
            'status'      => 'active',
            'approved_by' => Auth::user()['id'],
            'approved_at' => date('Y-m-d H:i:s'),
        ], ['id' => $id]);
        AuditLogger::log('APPROVE', 'vendors', null, (string)$id, ['status' => $vendor['status']], ['status' => 'active']);
        Helpers::jsonOk('Contractor approved and activated.');
        break;

    // ----------------------------------------------------------------
    case 'suspend':
        DB::update('vendors', ['status' => 'suspended'], ['id' => $id]);
        AuditLogger::log('SUSPEND', 'vendors', null, (string)$id, ['status' => $vendor['status']], ['status' => 'suspended']);
        Helpers::jsonOk('Contractor suspended.');
        break;

    // ----------------------------------------------------------------
    default:
        Helpers::jsonFail('Unknown action.');
}
