<?php
/**
 * CLMS 2.0 — Employee POST Actions (approve, delete)
 */
Auth::requireRole('hr_admin');
Auth::requireCsrf();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    Helpers::jsonFail('Invalid request.');
}

$action = Helpers::clean($_POST['action'] ?? '');

// Support /employees/{id}/approve and /employees/{id}/delete URL patterns
$urlId  = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$id     = (int)($_POST['id'] ?? $urlId);

// Detect action from URL if not in POST body
if (empty($action)) {
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    if (str_ends_with($uri, '/approve'))  $action = 'approve';
    if (str_ends_with($uri, '/delete'))   $action = 'delete';
}

if (!$id) Helpers::jsonFail('Invalid employee ID.');

$emp = DB::row('SELECT * FROM employees WHERE id = ? AND company_id = ?', [$id, $_SESSION['company_id']]);
if (!$emp) Helpers::jsonFail('Employee not found.');

switch ($action) {
    case 'approve':
        DB::update('employees', [
            'status'      => 'active',
            'approved_by' => Auth::user()['id'],
            'approved_at' => date('Y-m-d H:i:s'),
        ], ['id' => $id]);
        AuditLogger::log('APPROVE', 'employees', $id,
            ['status' => $emp['status']], ['status' => 'active']);
        Helpers::jsonOk('Employee approved and activated.');
        break;

    case 'deactivate':
        DB::update('employees', ['status' => 'inactive'], ['id' => $id]);
        AuditLogger::log('DEACTIVATE', 'employees', $id,
            ['status' => $emp['status']], ['status' => 'inactive']);
        Helpers::jsonOk('Employee deactivated.');
        break;

    case 'delete':
        // Soft-check: only delete pending or inactive
        if (!in_array($emp['status'], ['pending_approval', 'inactive'])) {
            Helpers::jsonFail('Only pending or inactive employees can be deleted. Separate active employees instead.');
        }
        if ($emp['photo_path']) {
            $path = CLMS_ROOT . '/' . ltrim($emp['photo_path'], '/');
            if (file_exists($path)) @unlink($path);
        }
        DB::delete('employees', ['id' => $id]);
        AuditLogger::log('DELETE', 'employees', $id, AuditLogger::sanitize($emp), null);
        Helpers::jsonOk('Employee record deleted.');
        break;

    default:
        Helpers::jsonFail('Unknown action.');
}
