<?php
/**
 * CLMS 2.0 — Attendance Index (Summary View)
 */
Auth::requireAuth();
$companyId = $_SESSION['company_id'];
$user      = Auth::user();

$date = Helpers::clean($_GET['date'] ?? date('Y-m-d'));
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) $date = date('Y-m-d');

$filterVendor  = (int)($_GET['vendor_id'] ?? 0);
$filterSection = (int)($_GET['section_id'] ?? 0);

$where = ['a.company_id=?', 'a.attendance_date=?'];
$bind  = [$companyId, $date];

if (Auth::hasRole('contractor') && !Auth::hasRole('hr_admin')) {
    $where[] = 'a.vendor_id=?';
    $bind[]  = $user['vendor_id'];
}
if ($filterVendor)  { $where[] = 'a.vendor_id=?';  $bind[] = $filterVendor; }
if ($filterSection) { $where[] = 'a.section_id=?'; $bind[] = $filterSection; }

$whereStr = implode(' AND ', $where);

$total   = (int) DB::value("SELECT COUNT(*) FROM attendance a WHERE $whereStr", $bind);
$present = (int) DB::value("SELECT COUNT(*) FROM attendance a WHERE $whereStr AND a.is_present=1", $bind);
$pager   = Helpers::paginate($total);

$records = DB::rows(
    "SELECT a.*, e.employee_code, e.first_name, e.last_name,
            v.name AS vendor_name, s.name AS section_name, sh.code AS shift_code
     FROM attendance a
     JOIN employees e ON e.id=a.employee_id
     JOIN vendors v   ON v.id=a.vendor_id
     JOIN sections s  ON s.id=a.section_id
     JOIN shifts sh   ON sh.id=a.shift_id
     WHERE $whereStr
     ORDER BY e.first_name, e.last_name
     LIMIT ? OFFSET ?",
    [...$bind, $pager['perPage'], $pager['offset']]
);

$vendors  = DB::rows("SELECT id, name FROM vendors WHERE company_id=? AND status='active' ORDER BY name", [$companyId]);
$sections = DB::rows("SELECT id, name FROM sections WHERE company_id=? AND is_active=1 ORDER BY name", [$companyId]);

ob_start();
?>
<div class="page-header">
  <div>
    <h1 class="page-title">Attendance</h1>
    <p class="page-sub" style="color:var(--clr-text-muted);margin:0;"><?= $date ?> &bull; <?= $present ?> present / <?= $total ?> records</p>
  </div>
  <div class="page-actions">
    <a href="<?= APP_BASE ?>/attendance/gate" class="btn btn-primary">Gate Entry</a>
    <a href="<?= APP_BASE ?>/attendance/biometric" class="btn btn-secondary">Biometric Upload</a>
  </div>
</div>

<div class="card">
  <div class="table-toolbar">
    <form method="GET" action="<?= APP_BASE ?>/attendance" style="display:flex;gap:var(--space-3);flex-wrap:wrap">
      <input type="date" name="date" class="form-control" value="<?= $date ?>" style="width:auto">
      <?php if (Auth::hasRole('hr_admin')): ?>
      <select name="vendor_id" class="form-control" style="width:auto">
        <option value="">All Vendors</option>
        <?php foreach ($vendors as $v): ?>
          <option value="<?= $v['id'] ?>" <?= $filterVendor==$v['id']?'selected':'' ?>><?= Helpers::h($v['name']) ?></option>
        <?php endforeach; ?>
      </select>
      <?php endif; ?>
      <select name="section_id" class="form-control" style="width:auto">
        <option value="">All Sections</option>
        <?php foreach ($sections as $s): ?>
          <option value="<?= $s['id'] ?>" <?= $filterSection==$s['id']?'selected':'' ?>><?= Helpers::h($s['name']) ?></option>
        <?php endforeach; ?>
      </select>
      <button type="submit" class="btn btn-secondary">Filter</button>
    </form>
  </div>
  <div class="table-wrapper">
    <table class="data-table">
      <thead>
        <tr><th>Employee</th><th>Vendor</th><th>Section</th><th>Shift</th><th>In</th><th>Out</th><th>Hours</th><th>Status</th></tr>
      </thead>
      <tbody>
        <?php foreach ($records as $a): ?>
        <tr>
          <td><?= Helpers::h($a['employee_code'].' — '.$a['first_name'].' '.$a['last_name']) ?></td>
          <td><?= Helpers::h($a['vendor_name']) ?></td>
          <td><?= Helpers::h($a['section_name']) ?></td>
          <td><?= Helpers::h($a['shift_code']) ?></td>
          <td><?= $a['in_time']  ? date('H:i', strtotime($a['in_time']))  : '—' ?></td>
          <td><?= $a['out_time'] ? date('H:i', strtotime($a['out_time'])) : '—' ?></td>
          <td><?= $a['worked_hours'] ? number_format($a['worked_hours'],2) : '—' ?></td>
          <td>
            <?php if ($a['is_present']): ?>
              <span class="badge badge-active">Present</span>
              <?php if ($a['is_overtime']): ?><span class="badge badge-pending" style="margin-left:2px">OT</span><?php endif; ?>
            <?php elseif ($a['is_half_day']): ?>
              <span class="badge badge-pending">Half Day</span>
            <?php else: ?>
              <span class="badge badge-inactive">Absent</span>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if (!$records): ?>
          <tr><td colspan="8" style="text-align:center;color:var(--clr-text-muted)">No attendance records for this date.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php
$pageContent = ob_get_clean();
$pageTitle   = 'Attendance';
$activeMenu  = 'attendance';
$breadcrumbs = [['label'=>'Attendance']];
include CLMS_ROOT . '/templates/base.html.php';
