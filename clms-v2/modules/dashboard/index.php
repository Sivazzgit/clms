<?php
/**
 * CLMS 2.0 — Dashboard
 * Role-aware stat cards + quick links.
 */

Auth::requireAuth();

$user    = Auth::user();
$roles   = $user['roles'];
$today   = date('Y-m-d');

// ----------------------------------------------------------------
// Collect stats (role-aware queries)
// ----------------------------------------------------------------
$stats   = [];
$alerts  = [];
$quick   = [];

if (Auth::hasRole('hr_admin')) {
    $stats[] = ['label' => 'Active Contractors',  'value' => DB::value("SELECT COUNT(*) FROM vendors WHERE status='active'"),                        'icon' => 'briefcase',       'color' => 'primary'];
    $stats[] = ['label' => 'Active Employees',    'value' => DB::value("SELECT COUNT(*) FROM employees WHERE status='active'"),                'icon' => 'id-card',         'color' => 'success'];
    $stats[] = ['label' => 'Open Indents',        'value' => DB::value("SELECT COUNT(*) FROM indents WHERE status NOT IN ('approved','closed')"), 'icon' => 'clipboard',    'color' => 'warning'];
    $stats[] = ['label' => "Today's Attendance",  'value' => DB::value("SELECT COUNT(*) FROM attendance WHERE attendance_date=? AND is_present=1", [$today]), 'icon' => 'calendar-check', 'color' => 'info'];

    $pending_employees = (int) DB::value("SELECT COUNT(*) FROM employees WHERE status='pending_approval'");
    if ($pending_employees > 0) {
        $alerts[] = ['type' => 'warning', 'msg' => "$pending_employees employee registration(s) pending approval.", 'url' => '/employees/pending'];
    }
    $pending_indents = (int) DB::value("SELECT COUNT(*) FROM indents WHERE status='plant_approved'");
    if ($pending_indents > 0) {
        $alerts[] = ['type' => 'info', 'msg' => "$pending_indents approved indent(s) pending vendor assignment.", 'url' => '/indent/assign'];
    }

    $quick = [
        ['label' => 'Add Contractor',    'url' => '/vendors/create',      'icon' => 'briefcase'],
        ['label' => 'Add Employee',      'url' => '/employees/create',    'icon' => 'id-card'],
        ['label' => 'New Indent',        'url' => '/indent/create',       'icon' => 'clipboard'],
        ['label' => 'Attendance Entry',  'url' => '/attendance/gate',     'icon' => 'calendar-check'],
        ['label' => 'Generate Bill',     'url' => '/billing/generate',    'icon' => 'file-invoice'],
        ['label' => 'Daily Report',      'url' => '/reports/daily-manpower','icon' => 'bar-chart'],
    ];
}

if (Auth::hasRole('contractor')) {
    $vid = $user['vendor_id'];
    $stats[] = ['label' => 'My Employees',        'value' => DB::value("SELECT COUNT(*) FROM employees WHERE vendor_id=? AND status='active'", [$vid]),    'icon' => 'id-card',         'color' => 'primary'];
    $stats[] = ['label' => 'Open Assignments',    'value' => DB::value("SELECT COUNT(*) FROM indent_vendor_assignments WHERE vendor_id=? AND status='open'", [$vid]), 'icon' => 'clipboard', 'color' => 'warning'];
    $stats[] = ['label' => "Today's Deployed",    'value' => DB::value("SELECT COUNT(*) FROM attendance WHERE vendor_id=? AND attendance_date=? AND is_present=1", [$vid, $today]), 'icon' => 'calendar-check', 'color' => 'success'];
    $stats[] = ['label' => 'Unpaid Bills',        'value' => DB::value("SELECT COUNT(*) FROM billing_periods WHERE vendor_id=? AND payment_status='pending'", [$vid]), 'icon' => 'file-invoice', 'color' => 'info'];
    $quick = [
        ['label' => 'My Employees',    'url' => '/employees',          'icon' => 'id-card'],
        ['label' => 'Submit Deployment','url' => '/deployment/create', 'icon' => 'users-check'],
        ['label' => 'My Bills',        'url' => '/billing',            'icon' => 'file-invoice'],
    ];
}

if (Auth::hasRole('section_incharge')) {
    $sectionIds = $user['section_ids'];
    $placeholders = implode(',', array_fill(0, max(1, count($sectionIds)), '?'));
    $stats[] = ['label' => 'My Pending Indents',      'value' => count($sectionIds) ? DB::value("SELECT COUNT(*) FROM indents WHERE section_id IN ($placeholders) AND status='draft'", $sectionIds) : 0, 'icon' => 'clipboard', 'color' => 'warning'];
    $stats[] = ['label' => "Today's Manpower",        'value' => count($sectionIds) ? DB::value("SELECT COUNT(*) FROM attendance WHERE section_id IN ($placeholders) AND attendance_date=? AND is_present=1", [...$sectionIds, $today]) : 0, 'icon' => 'calendar-check', 'color' => 'success'];
    $quick = [
        ['label' => 'Create Indent',     'url' => '/indent/create',       'icon' => 'clipboard'],
        ['label' => 'Gate Entry',        'url' => '/attendance/gate',     'icon' => 'calendar-check'],
    ];
}

if (Auth::hasRole('hod')) {
    $stats[] = ['label' => 'Indent Approvals Pending',  'value' => DB::value("SELECT COUNT(*) FROM indents WHERE status='pending_hod'"),            'icon' => 'clipboard',   'color' => 'warning'];
    $stats[] = ['label' => 'Deployment Approvals',      'value' => DB::value("SELECT COUNT(*) FROM deployment_plans WHERE status='pending_hod'"),    'icon' => 'users-check', 'color' => 'info'];
    $stats[] = ['label' => "Today's Total Manpower",    'value' => DB::value("SELECT COUNT(*) FROM attendance WHERE attendance_date=? AND is_present=1", [$today]), 'icon' => 'calendar-check', 'color' => 'success'];
    $quick = [
        ['label' => 'Indent Approvals',   'url' => '/indent/approve',      'icon' => 'clipboard'],
        ['label' => 'Deployment Review',  'url' => '/deployment/approve',  'icon' => 'users-check'],
    ];
}

if (Auth::hasRole('plant_head')) {
    $stats[] = ['label' => 'Final Indent Approvals',   'value' => DB::value("SELECT COUNT(*) FROM indents WHERE status='pending_plant_head'"),           'icon' => 'clipboard',   'color' => 'warning'];
    $stats[] = ['label' => 'Final Deployment Approvals','value' => DB::value("SELECT COUNT(*) FROM deployment_plans WHERE status='pending_plant_head'"),  'icon' => 'users-check', 'color' => 'info'];
    $stats[] = ['label' => "Today's Total Manpower",    'value' => DB::value("SELECT COUNT(*) FROM attendance WHERE attendance_date=? AND is_present=1", [$today]), 'icon' => 'calendar-check', 'color' => 'success'];
    $quick = [
        ['label' => 'Final Indent Approval',  'url' => '/indent/final-approve',  'icon' => 'clipboard'],
        ['label' => 'Daily Cost Report',      'url' => '/reports/daily-cost',    'icon' => 'bar-chart'],
    ];
}

if (Auth::hasRole('gate_staff')) {
    $stats[] = ['label' => "Today's Entries",     'value' => DB::value("SELECT COUNT(*) FROM attendance WHERE attendance_date=?", [$today]),                              'icon' => 'calendar-check', 'color' => 'primary'];
    $stats[] = ['label' => 'Present Today',       'value' => DB::value("SELECT COUNT(*) FROM attendance WHERE attendance_date=? AND is_present=1", [$today]),         'icon' => 'users-check',    'color' => 'success'];
    $stats[] = ['label' => 'Absent Today',        'value' => DB::value("SELECT COUNT(*) FROM attendance WHERE attendance_date=? AND is_absent=1", [$today]),           'icon' => 'id-card',        'color' => 'danger'];
    $quick = [
        ['label' => 'Gate Entry', 'url' => '/attendance/gate', 'icon' => 'calendar-check'],
    ];
}

// ----------------------------------------------------------------
// Recent activity (last 5 audit entries for this user)
// ----------------------------------------------------------------
$recentActivity = DB::rows(
    "SELECT action, module, record_id, created_at
     FROM audit_log WHERE user_id = ? ORDER BY id DESC LIMIT 5",
    [$user['id']]
);

// ----------------------------------------------------------------
// Render
// ----------------------------------------------------------------
ob_start();
?>

<div class="page-header">
  <div>
    <h1 class="page-title">
      <?php
        $hour = (int)date('H');
        if ($hour < 12) echo 'Good morning';
        elseif ($hour < 17) echo 'Good afternoon';
        else echo 'Good evening';
      ?>, <?= Helpers::h(explode(' ', $user['full_name'])[0]) ?>
    </h1>
    <p class="page-sub" style="color:var(--clr-text-muted);margin:0;">
      <?= Helpers::h(date('l, j F Y')) ?>
    </p>
  </div>
</div>

<!-- Alerts -->
<?php foreach ($alerts as $al): ?>
<div class="alert alert-<?= $al['type'] ?>" style="margin-bottom:var(--space-4)">
  <?= Helpers::h($al['msg']) ?>
  <a href="<?= Helpers::h($al['url']) ?>" style="margin-left:var(--space-3);font-weight:600;">View &rarr;</a>
</div>
<?php endforeach; ?>

<!-- Stat cards -->
<div class="stats-grid" style="margin-bottom:var(--space-8)">
  <?php foreach ($stats as $s): ?>
  <div class="stat-card">
    <div class="stat-icon stat-icon--<?= $s['color'] ?>">
      <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
        <?php
          $icons = [
            'briefcase'      => '<rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>',
            'id-card'        => '<rect x="2" y="5" width="20" height="14" rx="2"/><circle cx="8.5" cy="12" r="2.5"/><path d="M14 9h4M14 12h4M14 15h2"/>',
            'clipboard'      => '<path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><rect x="8" y="2" width="8" height="4" rx="1"/>',
            'calendar-check' => '<rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/><polyline points="9 16 11 18 15 14"/>',
            'file-invoice'   => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>',
            'users-check'    => '<path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><polyline points="17 11 19 13 23 9"/>',
            'bar-chart'      => '<line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/><line x1="2" y1="20" x2="22" y2="20"/>',
          ];
          echo $icons[$s['icon']] ?? '';
        ?>
      </svg>
    </div>
    <div class="stat-content">
      <div class="stat-value"><?= number_format((int)($s['value'] ?? 0)) ?></div>
      <div class="stat-label"><?= Helpers::h($s['label']) ?></div>
    </div>
  </div>
  <?php endforeach; ?>
</div>

<!-- Quick Actions + Recent Activity (2-column) -->
<div style="display:grid;grid-template-columns:1fr 1fr;gap:var(--space-6)">

  <!-- Quick Actions -->
  <?php if ($quick): ?>
  <div class="card">
    <div class="card-header">
      <h2 class="card-title">Quick Actions</h2>
    </div>
    <div class="card-body" style="padding:var(--space-4)">
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:var(--space-3)">
        <?php foreach ($quick as $q): ?>
        <a href="<?= Helpers::h($q['url']) ?>" class="btn btn-secondary" style="justify-content:flex-start;gap:var(--space-2)">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <?php
              $qicons = [
                'briefcase'      => '<rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>',
                'id-card'        => '<rect x="2" y="5" width="20" height="14" rx="2"/><circle cx="8.5" cy="12" r="2.5"/><path d="M14 9h4M14 12h4M14 15h2"/>',
                'clipboard'      => '<path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><rect x="8" y="2" width="8" height="4" rx="1"/>',
                'calendar-check' => '<rect x="3" y="4" width="18" height="18" rx="2"/><polyline points="9 16 11 18 15 14"/>',
                'file-invoice'   => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/>',
                'users-check'    => '<path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><polyline points="17 11 19 13 23 9"/>',
                'bar-chart'      => '<line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/>',
              ];
              echo $qicons[$q['icon']] ?? '';
            ?>
          </svg>
          <?= Helpers::h($q['label']) ?>
        </a>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
  <?php endif; ?>

  <!-- Recent Activity -->
  <div class="card">
    <div class="card-header">
      <h2 class="card-title">Recent Activity</h2>
    </div>
    <div class="card-body" style="padding:0">
      <?php if ($recentActivity): ?>
      <ul style="list-style:none;margin:0;padding:0">
        <?php foreach ($recentActivity as $act): ?>
        <li style="padding:var(--space-3) var(--space-4);border-bottom:var(--border-base);display:flex;justify-content:space-between;align-items:center;font-size:var(--text-sm)">
          <span>
            <span class="badge badge-<?= $act['action'] === 'DELETE' ? 'rejected' : ($act['action'] === 'LOGIN' ? 'approved' : 'pending') ?>">
              <?= Helpers::h($act['action']) ?>
            </span>
            &nbsp;
            <?= Helpers::h(ucfirst($act['module'])) ?>
            <?php if ($act['record_id']): ?>
              #<?= Helpers::h($act['record_id']) ?>
            <?php endif; ?>
          </span>
          <span style="color:var(--clr-text-muted)"><?= Helpers::dateDisplay($act['created_at']) ?></span>
        </li>
        <?php endforeach; ?>
      </ul>
      <?php else: ?>
        <p style="padding:var(--space-6);text-align:center;color:var(--clr-text-muted)">No recent activity.</p>
      <?php endif; ?>
    </div>
  </div>

</div><!-- grid -->

<?php
$pageContent = ob_get_clean();
$pageTitle   = 'Dashboard';
$activeMenu  = 'dashboard';
$breadcrumbs = [['label' => 'Dashboard']];
include CLMS_ROOT . '/templates/base.html.php';
