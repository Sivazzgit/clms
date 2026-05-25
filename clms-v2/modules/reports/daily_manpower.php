<?php
/**
 * CLMS 2.0 — Daily Manpower Report
 */
Auth::requireAuth();
$companyId = $_SESSION['company_id'];

$fromDate  = Helpers::clean($_GET['from_date'] ?? date('Y-m-01'));
$toDate    = Helpers::clean($_GET['to_date']   ?? date('Y-m-d'));
$sectionId = (int)($_GET['section_id'] ?? 0);
$vendorId  = (int)($_GET['vendor_id']  ?? 0);

$where = ['a.company_id=?','a.attendance_date BETWEEN ? AND ?'];
$bind  = [$companyId,$fromDate,$toDate];
if ($sectionId) { $where[] = 'a.section_id=?'; $bind[] = $sectionId; }
if ($vendorId)  { $where[] = 'a.vendor_id=?';  $bind[] = $vendorId; }
if (Auth::hasRole('contractor') && !Auth::hasRole('hr_admin')) { $where[] = 'a.vendor_id=?'; $bind[] = Auth::user()['vendor_id']; }
$whereStr = implode(' AND ', $where);

$records = DB::rows(
    "SELECT a.attendance_date, s.name AS section_name, sh.code AS shift_code,
            SUM(a.is_present) AS present, SUM(a.is_absent) AS absent, COUNT(*) AS total
     FROM attendance a
     JOIN sections s ON s.id=a.section_id
     JOIN shifts sh  ON sh.id=a.shift_id
     WHERE $whereStr
     GROUP BY a.attendance_date, a.section_id, a.shift_id
     ORDER BY a.attendance_date, s.name, sh.code",
    $bind
);

$sections = DB::rows("SELECT id,name FROM sections WHERE company_id=? AND is_active=1 ORDER BY name", [$companyId]);
$vendors  = DB::rows("SELECT id,name FROM vendors WHERE company_id=? AND status='active' ORDER BY name", [$companyId]);

ob_start();
?>
<div class="page-header">
  <div><h1 class="page-title">Daily Manpower</h1></div>
  <div class="page-actions"><a href="/reports" class="btn btn-ghost">← Reports</a></div>
</div>

<div class="card">
  <div class="table-toolbar">
    <form method="GET" action="/reports/daily-manpower" style="display:flex;gap:var(--space-3);flex-wrap:wrap">
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
      <thead><tr><th>Date</th><th>Section</th><th>Shift</th><th>Present</th><th>Absent</th><th>Total</th></tr></thead>
      <tbody>
        <?php foreach ($records as $r): ?>
        <tr>
          <td><?= Helpers::dateDisplay($r['attendance_date']) ?></td>
          <td><?= Helpers::h($r['section_name']) ?></td>
          <td><?= Helpers::h($r['shift_code']) ?></td>
          <td><?= $r['present'] ?></td>
          <td><?= $r['absent'] ?></td>
          <td><?= $r['total'] ?></td>
        </tr>
        <?php endforeach; ?>
        <?php if (!$records): ?><tr><td colspan="6" style="text-align:center;color:var(--clr-text-muted)">No data.</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php
$pageContent = ob_get_clean();
$pageTitle   = 'Daily Manpower';
$activeMenu  = 'reports';
$breadcrumbs = [['label'=>'Reports','url'=>'/reports'],['label'=>'Daily Manpower']];
include CLMS_ROOT . '/templates/base.html.php';
