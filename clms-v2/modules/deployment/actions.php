<?php
/**
 * CLMS 2.0 — Deployment Actions (POST handler)
 */
Auth::requireAuth();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') Helpers::redirect('/deployment');
Auth::requireCsrf();

$companyId = $_SESSION['company_id'];
$userId    = Auth::user()['id'];
$planId    = (int)($_POST['plan_id'] ?? 0);
$action    = Helpers::clean($_POST['action'] ?? '');
$remarks   = Helpers::clean($_POST['remarks'] ?? '');

$plan = DB::row("SELECT * FROM deployment_plans WHERE id=? AND company_id=?", [$planId,$companyId]);
if (!$plan) Helpers::redirect('/deployment', 'Plan not found.', 'error');

switch ($action) {
    case 'ic_review':
        Auth::requireRole('section_incharge');
        if ($plan['status'] !== 'submitted') Helpers::redirect("/deployment/$planId/view", 'Invalid action.', 'error');
        DB::execute("UPDATE deployment_plans SET status='ic_reviewed',updated_at=NOW() WHERE id=?", [$planId]);
        DB::execute("INSERT INTO deployment_approvals (plan_id,step,action,actor_id,remarks,acted_at) VALUES (?,'ic','reviewed',?,?,NOW())",
            [$planId,$userId,$remarks]);
        AuditLogger::log('APPROVE','deployment',$planId,['status'=>'submitted'],['status'=>'ic_reviewed']);
        Helpers::redirect("/deployment/$planId/view", 'IC review recorded.', 'success');
        break;

    case 'hod_accept':
        Auth::requireRole('hod');
        if ($plan['status'] !== 'ic_reviewed') Helpers::redirect("/deployment/$planId/view", 'Invalid action.', 'error');
        DB::execute("UPDATE deployment_plans SET status='hod_accepted',updated_at=NOW() WHERE id=?", [$planId]);
        DB::execute("INSERT INTO deployment_approvals (plan_id,step,action,actor_id,remarks,acted_at) VALUES (?,'hod','accepted',?,?,NOW())",
            [$planId,$userId,$remarks]);
        AuditLogger::log('APPROVE','deployment',$planId,['status'=>'ic_reviewed'],['status'=>'hod_accepted']);
        Helpers::redirect("/deployment/$planId/view", 'HOD accepted.', 'success');
        break;

    case 'plant_approve':
        Auth::requireRole('plant_head');
        if ($plan['status'] !== 'hod_accepted') Helpers::redirect("/deployment/$planId/view", 'Invalid action.', 'error');
        DB::execute("UPDATE deployment_plans SET status='plant_approved',updated_at=NOW() WHERE id=?", [$planId]);
        DB::execute("INSERT INTO deployment_approvals (plan_id,step,action,actor_id,remarks,acted_at) VALUES (?,'plant','approved',?,?,NOW())",
            [$planId,$userId,$remarks]);
        AuditLogger::log('APPROVE','deployment',$planId,['status'=>'hod_accepted'],['status'=>'plant_approved']);
        Helpers::redirect("/deployment/$planId/view", 'Plan approved.', 'success');
        break;

    case 'reject':
        Auth::requireRole(['section_incharge','hod','plant_head']);
        $step = ['submitted'=>'ic','ic_reviewed'=>'hod','hod_accepted'=>'plant'][$plan['status']] ?? null;
        if (!$step) Helpers::redirect("/deployment/$planId/view", 'Cannot reject at this stage.', 'error');
        DB::execute("UPDATE deployment_plans SET status='rejected',updated_at=NOW() WHERE id=?", [$planId]);
        DB::execute("INSERT INTO deployment_approvals (plan_id,step,action,actor_id,remarks,acted_at) VALUES (?,?,'rejected',?,?,NOW())",
            [$planId,$step,$userId,$remarks]);
        AuditLogger::log('REJECT','deployment',$planId,['status'=>$plan['status']],['status'=>'rejected']);
        Helpers::redirect("/deployment/$planId/view", 'Plan rejected.', 'success');
        break;

    default:
        Helpers::redirect("/deployment/$planId/view", 'Unknown action.', 'error');
}
