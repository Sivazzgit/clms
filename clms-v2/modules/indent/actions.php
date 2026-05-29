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
        if (!in_array($indent['status'], ['draft','needs_revision'])) break;
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

    case 'send_back':
        Auth::requireRole(['hod','plant_head','hr_admin']);
        $sendBackAllowed = [
            'hod'        => ['submitted'],
            'plant_head' => ['hod_reviewed'],
            'hr_admin'   => ['submitted','hod_reviewed','plant_approved'],
        ];
        $canSendBack = false;
        foreach ($sendBackAllowed as $role => $statuses) {
            if (Auth::hasRole($role) && in_array($indent['status'], $statuses)) {
                $canSendBack = true; break;
            }
        }
        if (!$canSendBack) Helpers::redirect($redirect, 'Cannot send back from current status.', 'error');
        if (!$remarks) Helpers::redirect($redirect, 'Please provide revision comments.', 'error');
        DB::execute("UPDATE indents SET status='needs_revision',updated_at=NOW() WHERE id=?", [$indentId]);
        recordApproval($indentId, 0, 'needs_revision', $userId, $remarks);
        AuditLogger::log('SENDBACK', 'indents', $indentId);
        Helpers::redirect($redirect, 'Indent sent back for revision with your comments.', 'success');

    case 'reject':
        Auth::requireRole(['hod','plant_head','hr_admin']);
        if (!in_array($indent['status'], ['submitted','hod_reviewed','plant_approved'])) break;
        if (!$remarks) Helpers::redirect($redirect, 'Please provide a reason for rejection.', 'error');
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
        Auth::requireRole(['hr_admin', 'section_incharge']);
        if (!in_array($indent['status'], ['hr_accepted', 'assigned', 'partially_confirmed'])) {
            Helpers::redirect('/indent/assign?id='.$indentId, 'Indent must be HR-accepted before assigning vendors.', 'error');
        }
        // Section incharge can only assign vendors for their own sections
        if (Auth::hasRole('section_incharge') && !Auth::hasRole('hr_admin')) {
            $mySections = array_column(
                DB::rows('SELECT section_id FROM user_sections WHERE user_id=?', [$userId]),
                'section_id'
            );
            if (!in_array($indent['section_id'], $mySections)) {
                Helpers::redirect('/indent/assign', 'You can only assign vendors to indents in your sections.', 'error');
            }
        }
        $lineId   = (int)($_POST['indent_line_id'] ?? 0);
        $vendorId = (int)($_POST['vendor_id']       ?? 0);
        $count    = (int)($_POST['assigned_count']  ?? 0);
        if (!$lineId || !$vendorId || $count < 1) {
            Helpers::redirect('/indent/assign?id='.$indentId, 'Line, vendor and count are required.', 'error');
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
        // Always move to 'assigned' when a new vendor is added (even from partially_confirmed)
        DB::execute("UPDATE indents SET status='assigned',updated_at=NOW() WHERE id=?", [$indentId]);
        AuditLogger::log('ASSIGN', 'indents', $indentId);
        Helpers::redirect('/indent/assign?id='.$indentId, 'Vendor assigned.', 'success');

    case 'remove_assignment':
        Auth::requireRole(['hr_admin', 'section_incharge']);
        $assignmentId = (int)($_POST['assignment_id'] ?? 0);
        if (!$assignmentId) Helpers::redirect($redirect, 'Invalid assignment.', 'error');
        $asgn = DB::row("SELECT * FROM indent_vendor_assignments WHERE id=? AND indent_id=?", [$assignmentId, $indentId]);
        if (!$asgn) Helpers::redirect($redirect, 'Assignment not found.', 'error');
        if (Auth::hasRole('section_incharge') && !Auth::hasRole('hr_admin')) {
            $mySec = array_column(DB::rows('SELECT section_id FROM user_sections WHERE user_id=?', [$userId]), 'section_id');
            if (!in_array($indent['section_id'], $mySec)) Helpers::redirect('/indent/assign', 'Not authorised.', 'error');
        }
        DB::execute("DELETE FROM indent_vendor_assignments WHERE id=?", [$assignmentId]);
        // Re-evaluate indent status after removal
        $remaining = (int) DB::value("SELECT COUNT(*) FROM indent_vendor_assignments WHERE indent_id=?", [$indentId]);
        if ($remaining === 0) {
            $newStatus = 'hr_accepted'; // no vendors left → back to awaiting assignment
        } else {
            $stillPending = (int) DB::value("SELECT COUNT(*) FROM indent_vendor_assignments WHERE indent_id=? AND status='assigned'", [$indentId]);
            if ($stillPending > 0) {
                $newStatus = 'assigned';
            } else {
                $totalRequired  = (int) DB::value("SELECT SUM(required_count) FROM indent_lines WHERE indent_id=?", [$indentId]);
                $totalConfirmed = (int) DB::value("SELECT SUM(confirmed_count) FROM indent_vendor_assignments WHERE indent_id=? AND status IN ('confirmed','partially_confirmed')", [$indentId]);
                $newStatus = ($totalConfirmed >= $totalRequired) ? 'contractor_confirmed' : 'partially_confirmed';
            }
        }
        DB::execute("UPDATE indents SET status=?,updated_at=NOW() WHERE id=?", [$newStatus, $indentId]);
        AuditLogger::log('REMOVE_ASSIGNMENT', 'indents', $indentId);
        Helpers::redirect('/indent/assign?id='.$indentId, 'Assignment removed.', 'success');

    case 'vendor_respond':
        Auth::requireRole('contractor');
        $vendorId = $_SESSION['vendor_id'] ?? 0;
        if (!$vendorId) Helpers::redirect($redirect, 'No vendor linked to your account.', 'error');
        if (!in_array($indent['status'], ['assigned','partially_confirmed']))
            Helpers::redirect($redirect, 'Indent is not awaiting vendor response.', 'error');

        // Load this vendor's assignment rows
        $assignRows = DB::rows(
            "SELECT id, assigned_count FROM indent_vendor_assignments WHERE indent_id=? AND vendor_id=? AND status IN ('assigned','partially_confirmed')",
            [$indentId, $vendorId]
        );
        if (!$assignRows) Helpers::redirect($redirect, 'No pending assignments found for your vendor.', 'error');

        $responseType    = $_POST['response_type'] ?? 'confirm'; // confirm | partial | reject
        $confirmedCounts = $_POST['confirmed_counts'] ?? [];     // [assignment_id => count]

        foreach ($assignRows as $row) {
            $aId = $row['id'];
            if ($responseType === 'confirm') {
                $cnt    = $row['assigned_count'];
                $status = 'confirmed';
            } elseif ($responseType === 'partial') {
                $cnt    = max(0, (int)($confirmedCounts[$aId] ?? 0));
                $status = $cnt > 0 ? 'partially_confirmed' : 'rejected';
            } else { // reject
                $cnt    = 0;
                $status = 'rejected';
            }
            DB::execute(
                "UPDATE indent_vendor_assignments SET status=?, confirmed_count=?, confirmed_at=NOW() WHERE id=?",
                [$status, $cnt, $aId]
            );
        }

        // Re-evaluate indent status
        $remaining = (int) DB::value(
            "SELECT COUNT(*) FROM indent_vendor_assignments WHERE indent_id=? AND status='assigned'",
            [$indentId]
        );
        if ($remaining > 0) {
            // Other vendors still unresponded
            $newStatus = 'assigned';
            $flash     = 'Response saved. Awaiting other vendor responses.';
        } else {
            // All vendors have responded — check if fully covered
            $totalRequired  = (int) DB::value("SELECT SUM(required_count) FROM indent_lines WHERE indent_id=?", [$indentId]);
            $totalConfirmed = (int) DB::value(
                "SELECT SUM(confirmed_count) FROM indent_vendor_assignments WHERE indent_id=? AND status IN ('confirmed','partially_confirmed')",
                [$indentId]
            );
            if ($totalConfirmed >= $totalRequired) {
                $newStatus = 'contractor_confirmed';
                $flash     = 'All requirements confirmed. Deployment can proceed.';
            } else {
                $newStatus = 'partially_confirmed';
                $flash     = 'Response saved. Only '.$totalConfirmed.' of '.$totalRequired.' workers confirmed. HR will assign additional vendors.';
            }
        }
        DB::execute("UPDATE indents SET status=?,updated_at=NOW() WHERE id=?", [$newStatus, $indentId]);
        $approvalAction = match($responseType) {
            'partial' => 'vendor_partial',
            'reject'  => 'vendor_rejected',
            default   => 'vendor_confirmed',
        };
        recordApproval($indentId, 5, $approvalAction, $userId, $remarks ?: ucfirst($responseType).' by contractor');
        AuditLogger::log('VENDOR_RESPOND', 'indents', $indentId);
        Helpers::redirect($redirect, $flash, $newStatus === 'partially_confirmed' ? 'warning' : 'success');
}
