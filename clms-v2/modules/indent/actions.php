<?php
/**
 * CLMS 2.0 — Indent Actions (POST only)
 * Handles: submit, hod_review, plant_approve, hr_accept, reject, cancel, assign_vendor
 */
Auth::requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    Helpers::redirect('/indent');
}
Auth::requireCsrf();

$companyId = $_SESSION['company_id'];
$indentId  = (int)($_POST['indent_id'] ?? 0);
$action    = Helpers::clean($_POST['action'] ?? '');
$remarks   = Helpers::clean($_POST['remarks'] ?? '');

$indent = DB::row("SELECT * FROM indents WHERE id=? AND company_id=?", [$indentId, $companyId]);
if (!$indent) {
    Helpers::jsonFail('Indent not found.');
}

function recordApproval(int $indentId, int $step, string $action, int $actorId, string $remarks = ''): void {
    DB::execute(
        "INSERT INTO indent_approvals (indent_id,step,action,actor_id,remarks) VALUES (?,?,?,?,?)",
        [$indentId, $step, $action, $actorId, $remarks ?: null]
    );
}

$userId = $_SESSION['user_id'];
$redirect = '/indent/'.$indentId.'/view';

switch ($action) {
    case 'submit':
        Auth::requireRole(['section_incharge','hr_admin']);
        if ($indent['status'] !== 'draft') break;
        DB::execute("UPDATE indents SET status='submitted',updated_at=NOW() WHERE id=?", [$indentId]);
        recordApproval($indentId, 1, 'submitted', $userId, $remarks);
        AuditLogger::log('SUBMIT', 'indents', $indentId);
        Helpers::redirect($redirect, 'Indent submitted for HOD review.', 'success');

    case 'hod_review':
        Auth::requireRole(['hod','hr_admin']);
        if ($indent['status'] !== 'submitted') break;
        DB::execute("UPDATE indents SET status='hod_reviewed',updated_at=NOW() WHERE id=?", [$indentId]);
        recordApproval($indentId, 2, 'hod_reviewed', $userId, $remarks);
        AuditLogger::log('APPROVE', 'indents', $indentId);
        Helpers::redirect($redirect, 'Indent reviewed and forwarded to Plant Head.', 'success');

    case 'plant_approve':
        Auth::requireRole(['plant_head','hr_admin']);
        if ($indent['status'] !== 'hod_reviewed') break;
        DB::execute("UPDATE indents SET status='plant_approved',updated_at=NOW() WHERE id=?", [$indentId]);
        recordApproval($indentId, 3, 'plant_approved', $userId, $remarks);
        AuditLogger::log('APPROVE', 'indents', $indentId);
        Helpers::redirect($redirect, 'Indent approved by Plant Head.', 'success');

    case 'hr_accept':
        Auth::requireRole('hr_admin');
        if ($indent['status'] !== 'plant_approved') break;
        DB::execute("UPDATE indents SET status='hr_accepted',updated_at=NOW() WHERE id=?", [$indentId]);
        recordApproval($indentId, 4, 'hr_accepted', $userId, $remarks);
        AuditLogger::log('APPROVE', 'indents', $indentId);
        Helpers::redirect($redirect, 'Indent accepted by HR. Ready for vendor assignment.', 'success');

    case 'reject':
        Auth::requireRole(['hod','plant_head','hr_admin']);
        if (!in_array($indent['status'], ['submitted','hod_reviewed','plant_approved'])) break;
        DB::execute("UPDATE indents SET status='rejected',updated_at=NOW() WHERE id=?", [$indentId]);
        recordApproval($indentId, 0, 'rejected', $userId, $remarks);
        AuditLogger::log('REJECT', 'indents', $indentId);
        Helpers::redirect($redirect, 'Indent rejected.', 'error');

    case 'cancel':
        Auth::requireRole(['section_incharge','hr_admin']);
        if (!in_array($indent['status'], ['draft','submitted'])) break;
        DB::execute("UPDATE indents SET status='cancelled',updated_at=NOW() WHERE id=?", [$indentId]);
        AuditLogger::log('CANCEL', 'indents', $indentId);
        Helpers::redirect('/indent', 'Indent cancelled.', 'success');

    case 'assign_vendor':
        Auth::requireRole('hr_admin');
        if ($indent['status'] !== 'hr_accepted') {
            Helpers::redirect($redirect, 'Indent must be HR-accepted before assigning vendors.', 'error');
        }
        $lineId   = (int)($_POST['indent_line_id'] ?? 0);
        $vendorId = (int)($_POST['vendor_id']       ?? 0);
        $count    = (int)($_POST['assigned_count']  ?? 0);
        if (!$lineId || !$vendorId || $count < 1) {
            Helpers::redirect($redirect, 'Line, vendor and count are required.', 'error');
        }
        // Upsert assignment
        $existing = DB::row("SELECT id FROM indent_vendor_assignments WHERE indent_id=? AND indent_line_id=? AND vendor_id=?", [$indentId,$lineId,$vendorId]);
        if ($existing) {
            DB::execute("UPDATE indent_vendor_assignments SET assigned_count=?,status='assigned',assigned_by=?,assigned_at=NOW() WHERE id=?",
                [$count,$userId,$existing['id']]);
        } else {
            DB::execute("INSERT INTO indent_vendor_assignments (indent_id,indent_line_id,vendor_id,assigned_count,assigned_by) VALUES (?,?,?,?,?)",
                [$indentId,$lineId,$vendorId,$count,$userId]);
        }
        DB::execute("UPDATE indents SET status='assigned',updated_at=NOW() WHERE id=?", [$indentId]);
        AuditLogger::log('ASSIGN', 'indents', $indentId);
        Helpers::redirect($redirect, 'Vendor assigned.', 'success');
}

Helpers::redirect($redirect, 'Action completed.', 'success');
