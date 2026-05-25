<?php
/**
 * CLMS 2.0 — Create / Edit Deployment Plan
 */
Auth::requireRole('contractor');
$companyId = $_SESSION['company_id'];
$vendorId  = Auth::user()['vendor_id'];
$errors    = [];

$planId = (int)($_GET['id'] ?? 0);
$plan   = $planId ? DB::row("SELECT * FROM deployment_plans WHERE id=? AND vendor_id=? AND company_id=?", [$planId,$vendorId,$companyId]) : null;
if ($planId && !$plan) { Helpers::redirect('/deployment'); }
if ($plan && !in_array($plan['status'], ['submitted'])) { Helpers::redirect('/deployment/'.$planId.'/view'); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Auth::requireCsrf();
    $action     = Helpers::clean($_POST['action'] ?? 'save');
    $indentId   = (int)($_POST['indent_id']  ?? 0);
    $shiftId    = (int)($_POST['shift_id']   ?? 0);
    $sectionId  = (int)($_POST['section_id'] ?? 0);
    $planDate   = Helpers::clean($_POST['plan_date'] ?? '');
    $employeeIds = array_filter(array_map('intval', $_POST['employee_ids'] ?? []));

    if (!$indentId)           $errors[] = 'Indent is required.';
    if (!$shiftId)            $errors[] = 'Shift is required.';
    if (!$sectionId)          $errors[] = 'Section is required.';
    if (!$planDate)           $errors[] = 'Plan date is required.';
    if (empty($employeeIds))  $errors[] = 'At least one employee is required.';

    if (!$errors) {
        if ($plan) {
            DB::execute(
                "UPDATE deployment_plans SET indent_id=?,shift_id=?,section_id=?,plan_date=?,updated_at=NOW() WHERE id=?",
                [$indentId,$shiftId,$sectionId,$planDate,$planId]
            );
            DB::execute("DELETE FROM deployment_plan_employees WHERE plan_id=?", [$planId]);
            $currentPlanId = $planId;
        } else {
            DB::execute(
                "INSERT INTO deployment_plans (company_id,indent_id,vendor_id,plan_date,shift_id,section_id,status,submitted_by,submitted_at,updated_at)
                 VALUES (?,?,?,?,?,?,'submitted',?,NOW(),NOW())",
                [$companyId,$indentId,$vendorId,$planDate,$shiftId,$sectionId,Auth::user()['id']]
            );
            $currentPlanId = DB::lastInsertId();
        }
        $uid = Auth::user()['id'];
        foreach ($employeeIds as $eId) {
            DB::execute("INSERT INTO deployment_plan_employees (plan_id,employee_id,added_by,added_at) VALUES (?,?,?,NOW())", [$currentPlanId,$eId,$uid]);
        }
        AuditLogger::log($plan ? 'UPDATE' : 'CREATE', 'deployment', $currentPlanId);
        Helpers::redirect('/deployment/'.$currentPlanId.'/view', 'Deployment plan saved.', 'success');
    }
}

// Data for form
$assignedIndents = DB::rows(
    "SELECT i.id, i.indent_no, i.start_date, i.end_date, s.name AS section_name
     FROM indent_vendor_assignments iva
     JOIN indents i ON i.id=iva.indent_id
     JOIN sections s ON s.id=i.section_id
     WHERE iva.vendor_id=? AND i.status IN ('assigned','contractor_confirmed')
     GROUP BY i.id ORDER BY i.start_date DESC",
    [$vendorId]
);

$shifts    = DB::rows("SELECT id,code,name FROM shifts WHERE company_id=? AND is_active=1 ORDER BY code", [$companyId]);
$sections  = DB::rows("SELECT id,code,name FROM sections WHERE company_id=? AND is_active=1 ORDER BY name", [$companyId]);

// My employees for this vendor
$employees = DB::rows(
    "SELECT id,employee_code,first_name,last_name,shift_id,section_id FROM employees
     WHERE vendor_id=? AND company_id=? AND status='active' ORDER BY first_name,last_name",
    [$vendorId,$companyId]
);

$existing = [];
if ($plan) {
    $exRows = DB::rows("SELECT employee_id FROM deployment_plan_employees WHERE plan_id=?", [$planId]);
    foreach ($exRows as $r) $existing[] = $r['employee_id'];
}

ob_start();
?>
<div class="page-header">
  <div><h1 class="page-title"><?= $plan ? 'Edit' : 'New' ?> Deployment Plan</h1></div>
  <div class="page-actions"><a href="<?= APP_BASE ?>/deployment" class="btn btn-ghost">Cancel</a></div>
</div>

<?php foreach ($errors as $e): ?>
  <div class="alert alert-danger" style="margin-bottom:var(--space-2)"><?= Helpers::h($e) ?></div>
<?php endforeach; ?>

<form method="POST" action="<?= APP_BASE ?>/deployment/<?= $plan ? $planId.'/edit' : 'create' ?>">
  <input type="hidden" name="csrf_token" value="<?= Helpers::h($_SESSION['csrf_token']) ?>">

  <div class="card" style="margin-bottom:var(--space-4)">
    <div class="card-header"><h3 class="card-title">Plan Details</h3></div>
    <div class="card-body">
      <div class="form-grid-3">
        <div class="form-group">
          <label class="form-label required">Indent</label>
          <select name="indent_id" class="form-control" required>
            <option value="">— Select Indent —</option>
            <?php foreach ($assignedIndents as $i): ?>
              <option value="<?= $i['id'] ?>" <?= ($plan['indent_id'] ?? '')==$i['id']?'selected':'' ?>>
                <?= Helpers::h($i['indent_no'].' ('.$i['section_name'].')') ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label required">Shift</label>
          <select name="shift_id" class="form-control" required>
            <option value="">— Select Shift —</option>
            <?php foreach ($shifts as $s): ?>
              <option value="<?= $s['id'] ?>" <?= ($plan['shift_id']??'')==$s['id']?'selected':'' ?>><?= Helpers::h($s['code'].' — '.$s['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label required">Section</label>
          <select name="section_id" class="form-control" required>
            <option value="">— Select Section —</option>
            <?php foreach ($sections as $s): ?>
              <option value="<?= $s['id'] ?>" <?= ($plan['section_id']??'')==$s['id']?'selected':'' ?>><?= Helpers::h($s['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label required">Deployment Date</label>
          <input type="date" name="plan_date" class="form-control" value="<?= Helpers::h($plan['plan_date'] ?? date('Y-m-d')) ?>" required>
        </div>
      </div>
    </div>
  </div>

  <div class="card" style="margin-bottom:var(--space-4)">
    <div class="card-header"><h3 class="card-title">Select Employees</h3></h3></div>
    <div class="table-wrapper">
      <table class="data-table">
        <thead><tr><th><input type="checkbox" id="chkAll"></th><th>Code</th><th>Name</th></tr></thead>
        <tbody>
          <?php foreach ($employees as $e): ?>
          <tr>
            <td><input type="checkbox" name="employee_ids[]" value="<?= $e['id'] ?>" <?= in_array($e['id'],$existing)?'checked':'' ?> class="emp-chk"></td>
            <td><?= Helpers::h($e['employee_code']) ?></td>
            <td><?= Helpers::h($e['first_name'].' '.$e['last_name']) ?></td>
          </tr>
          <?php endforeach; ?>
          <?php if (!$employees): ?>
            <tr><td colspan="3" style="color:var(--clr-text-muted)">No active employees found for your organisation.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <div class="form-actions">
    <button type="submit" name="action" value="save" class="btn btn-primary">Submit Plan</button>
  </div>
</form>

<script>
document.getElementById('chkAll').addEventListener('change', function(){
  document.querySelectorAll('.emp-chk').forEach(c => c.checked = this.checked);
});
</script>

<?php
$pageContent = ob_get_clean();
$pageTitle   = ($plan ? 'Edit' : 'New') . ' Deployment Plan';
$activeMenu  = 'deployment';
$breadcrumbs = [['label'=>'Deployment','url'=>'/deployment'],['label'=>$plan?'Edit':'New Plan']];
include CLMS_ROOT . '/templates/base.html.php';
