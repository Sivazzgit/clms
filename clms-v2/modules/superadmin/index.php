<?php
/**
 * CLMS 2.0 — Super Admin Dashboard
 * GET /superadmin
 * Shows all tenants (companies) with stats. Entry point for super admin.
 */
Auth::requireRole('super_admin');

$companies = DB::rows(
    "SELECT c.id, c.code, c.name, c.city, c.state, c.is_active,
            COUNT(DISTINCT u.id)  AS user_count,
            COUNT(DISTINCT v.id)  AS vendor_count,
            COUNT(DISTINCT e.id)  AS employee_count
     FROM companies c
     LEFT JOIN users       u ON u.company_id = c.id AND u.is_active = 1
     LEFT JOIN vendors     v ON v.company_id = c.id AND v.status = 'active'
     LEFT JOIN employees   e ON e.company_id = c.id AND e.status = 'active'
     GROUP BY c.id
     ORDER BY c.name"
);

ob_start();
?>
<div class="page-header">
  <div>
    <h1 class="page-title">Super Admin — Platform Overview</h1>
    <p class="page-sub" style="color:var(--clr-text-muted);margin:0">
      All tenants (companies) on this CLMS instance.
    </p>
  </div>
</div>

<!-- Stats row -->
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:var(--space-4);margin-bottom:var(--space-6)">
  <div class="card" style="padding:var(--space-4)">
    <div style="font-size:var(--text-xs);color:var(--clr-text-muted);text-transform:uppercase;letter-spacing:.05em">Companies</div>
    <div style="font-size:var(--text-3xl);font-weight:var(--fw-bold);margin-top:var(--space-1)"><?= count($companies) ?></div>
  </div>
</div>

<div class="card">
  <div class="table-wrapper">
    <table class="data-table">
      <thead>
        <tr>
          <th>#</th>
          <th>Code</th>
          <th>Company</th>
          <th>City / State</th>
          <th>Users</th>
          <th>Vendors</th>
          <th>Employees</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($companies as $co): ?>
        <tr>
          <td><?= $co['id'] ?></td>
          <td><code><?= Helpers::h($co['code']) ?></code></td>
          <td><?= Helpers::h($co['name']) ?></td>
          <td><?= Helpers::h($co['city'] . ', ' . $co['state']) ?></td>
          <td><?= number_format($co['user_count']) ?></td>
          <td><?= number_format($co['vendor_count']) ?></td>
          <td><?= number_format($co['employee_count']) ?></td>
          <td>
            <?php if ($co['is_active']): ?>
              <span class="badge badge-active">Active</span>
            <?php else: ?>
              <span class="badge badge-inactive">Inactive</span>
            <?php endif; ?>
          </td>
          <td>
            <a href="/superadmin/switch-company?company_id=<?= $co['id'] ?>"
               class="btn btn-secondary btn-sm"
               title="Switch to this tenant and browse as super admin">
              Enter company
            </a>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if (!$companies): ?>
        <tr><td colspan="9" style="text-align:center;color:var(--clr-text-muted)">No companies found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<div style="margin-top:var(--space-4)">
  <a href="/superadmin/impersonation-log" class="btn btn-ghost">View Impersonation Log</a>
</div>

<?php
$pageContent = ob_get_clean();
$pageTitle   = 'Super Admin';
$activeMenu  = 'superadmin';
$breadcrumbs = [['label' => 'Super Admin']];
include CLMS_ROOT . '/templates/base.html.php';
