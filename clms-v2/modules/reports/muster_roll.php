<?php
/**
 * CLMS 2.0 — Muster Roll (daily attendance register)
 */
Auth::requireAuth();
$companyId = $_SESSION['company_id'];

$fromDate  = Helpers::clean($_GET['from_date']  ?? date('Y-m-01'));
$toDate    = Helpers::clean($_GET['to_date']    ?? date('Y-m-d'));
$sectionId = (int)($_GET['section_id'] ?? 0);
$vendorId  = (int)($_GET['vendor_id']  ?? 0);

$where = ['a.company_id=?'];
$bind  = [$companyId];
if ($fromDate)  { $where[] = 'a.attendance_date>=?'; $bind[] = $fromDate; }
if ($toDate)    { $where[] = 'a.attendance_date<=?'; $bind[] = $toDate; }
if ($sectionId) { $where[] = 'a.section_id=?'; $bind[] = $sectionId; }
if ($vendorId)  { $where[] = 'a.vendor_id=?';  $bind[] = $vendorId; }

// Restrict contractors
if (Auth::hasRole('contractor') && !Auth::hasRole('hr_admin')) {
    $where[] = 'a.vendor_id=?'; $bind[] = Auth::user()['vendor_id'];
}

$whereStr = implode(' AND ', $where);

$records = DB::rows(
    "SELECT a.attendance_date, e.employee_code, e.first_name, e.last_name,
            s.name AS section_name, sh.code AS shift_code, v.name AS vendor_name,
            a.is_present, a.is_absent, a.is_half_day, a.worked_hours,
            a.in_time, a.out_time, a.source
     FROM attendance a
     JOIN employees e ON e.id=a.employee_id
     JOIN sections s  ON s.id=a.section_id
     JOIN shifts sh   ON sh.id=a.shift_id
     JOIN vendors v   ON v.id=a.vendor_id
     WHERE $whereStr
     ORDER BY a.attendance_date, s.name, e.first_name",
    $bind
);

$sections = DB::rows("SELECT id,name FROM sections WHERE company_id=? AND is_active=1 ORDER BY name", [$companyId]);
$vendors  = DB::rows("SELECT id,name FROM vendors WHERE company_id=? AND status='active' ORDER BY name", [$companyId]);

ob_start();
?>
<div class="page-header">
  <div><h1 class="page-title">Muster Roll</h1></div>
  <div class="page-actions"><a href="<?= APP_BASE ?>/reports" class="btn btn-ghost">← Reports</a></div>
</div>

<div class="card">
  <div class="table-toolbar">
    <form method="GET" action="<?= APP_BASE ?>/reports/muster-roll" style="display:flex;gap:var(--space-3);flex-wrap:wrap">
      <input type="date" name="from_date" class="form-control" value="<?= $fromDate ?>">
      <input type="date" name="to_date"   class="form-control" value="<?= $toDate ?>">
      <select name="section_id" class="form-control" style="width:auto">
        <option value="">All Sections</option>
        <?php foreach ($sections as $s): ?><option value="<?= $s['id'] ?>" <?= $sectionId==$s['id']?'selected':'' ?>><?= Helpers::h($s['name']) ?></option><?php endforeach; ?>
      </select>
      <?php if (!Auth::hasRole('contractor')): ?>
      <select name="vendor_id" class="form-control" style="width:auto">
        <option value="">All Vendors</option>
        <?php foreach ($vendors as $v): ?><option value="<?= $v['id'] ?>" <?= $vendorId==$v['id']?'selected':'' ?>><?= Helpers::h($v['name']) ?></option><?php endforeach; ?>
      </select>
      <?php endif; ?>
      <button type="submit" class="btn btn-secondary">Apply</button>
    </form>
  </div>
  <div class="table-wrapper">
    <table class="data-table">
      <thead>
        <tr><th>Date</th><th>Employee</th><th>Section</th><th>Shift</th><th>Vendor</th><th>In</th><th>Out</th><th>Hours</th><th>Status</th></tr>
      </thead>
      <tbody>
        <?php foreach ($records as $r): ?>
        <tr>
          <td><?= Helpers::dateDisplay($r['attendance_date']) ?></td>
          <td><?= Helpers::h($r['employee_code'].' — '.$r['first_name'].' '.$r['last_name']) ?></td>
          <td><?= Helpers::h($r['section_name']) ?></td>
          <td><?= Helpers::h($r['shift_code']) ?></td>
          <td><?= Helpers::h($r['vendor_name']) ?></td>
          <td><?= $r['in_time']  ? date('H:i', strtotime($r['in_time']))  : '—' ?></td>
          <td><?= $r['out_time'] ? date('H:i', strtotime($r['out_time'])) : '—' ?></td>
          <td><?= $r['worked_hours'] ? number_format((float)$r['worked_hours'],2) : '—' ?></td>
          <td>
            <?php if ($r['is_present']): ?>
              <span class="badge badge-active">P</span>
            <?php elseif ($r['is_half_day']): ?>
              <span class="badge badge-pending">H</span>
            <?php else: ?>
              <span class="badge badge-inactive">A</span>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if (!$records): ?><tr><td colspan="9" style="text-align:center;color:var(--clr-text-muted)">No records found.</td></tr><?php endif; ?>
      </tbody>
    </table>
    <div style="padding:var(--space-3);color:var(--clr-text-muted);font-size:var(--text-sm)">Total records: <?= count($records) ?></div>
  </div>
</div>

<?php
$pageContent = ob_get_clean();
$pageTitle   = 'Muster Roll';
$activeMenu  = 'reports';
$breadcrumbs = [['label'=>'Reports','url'=>'/reports'],['label'=>'Muster Roll']];
include CLMS_ROOT . '/templates/base.html.php';
