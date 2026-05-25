<?php
/**
 * CLMS 2.0 — Reports Landing Page
 */
Auth::requireAuth();

$reports = [
    ['group' => 'Statutory Reports', 'items' => [
        ['title'=>'Muster Roll',        'desc'=>'Daily attendance register per section/contractor.',    'url'=>'/reports/muster-roll',     'icon'=>'📋'],
        ['title'=>'Wage Register',      'desc'=>'Monthly wage register with components per employee.',  'url'=>'/reports/wage-register',   'icon'=>'💰'],
        ['title'=>'PF & ESI Register',  'desc'=>'PF & ESI deduction register.',                        'url'=>'/reports/pf-esi',          'icon'=>'🏛️'],
        ['title'=>'Form 12-A',          'desc'=>'Half-yearly return of workmen employed.',              'url'=>'/reports/form12a',         'icon'=>'📄'],
        ['title'=>'Adult Workers',      'desc'=>'Register of adult contract workers.',                  'url'=>'/reports/adult-workers',   'icon'=>'👷'],
    ]],
    ['group' => 'Operational Reports', 'items' => [
        ['title'=>'Daily Manpower',     'desc'=>'Day-wise manpower count by section/shift.',            'url'=>'/reports/daily-manpower',  'icon'=>'📊'],
        ['title'=>'Indent vs Deployment','desc'=>'Compare required vs deployed headcount.',             'url'=>'/reports/indent-deployment','icon'=>'🔄'],
        ['title'=>'Absenteeism',        'desc'=>'Absent employees per day/week.',                       'url'=>'/reports/absenteeism',     'icon'=>'❌'],
    ]],
    ['group' => 'Cost Reports', 'items' => [
        ['title'=>'Cost by Contractor', 'desc'=>'Monthly cost summary per contractor.',                 'url'=>'/reports/cost-contractor', 'icon'=>'🏢'],
        ['title'=>'Cost by Section',    'desc'=>'Monthly cost summary per section.',                    'url'=>'/reports/cost-section',    'icon'=>'🗂️'],
        ['title'=>'Daily Cost',         'desc'=>'Daily labour cost for a period.',                      'url'=>'/reports/daily-cost',      'icon'=>'📈'],
    ]],
];

ob_start();
?>
<div class="page-header">
  <div><h1 class="page-title">Reports</h1></div>
</div>

<?php foreach ($reports as $group): ?>
<h2 style="margin:var(--space-6) 0 var(--space-3);font-size:var(--text-lg);font-weight:600"><?= $group['group'] ?></h2>
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:var(--space-4);margin-bottom:var(--space-4)">
  <?php foreach ($group['items'] as $r): ?>
  <a href="<?= $r['url'] ?>" style="text-decoration:none;color:inherit">
    <div class="card" style="height:100%;transition:box-shadow .15s" onmouseover="this.style.boxShadow='0 4px 16px rgba(0,0,0,.1)'" onmouseout="this.style.boxShadow=''">
      <div class="card-body" style="display:flex;gap:var(--space-3)">
        <span style="font-size:1.8rem;line-height:1"><?= $r['icon'] ?></span>
        <div>
          <div style="font-weight:600;margin-bottom:4px"><?= Helpers::h($r['title']) ?></div>
          <div style="font-size:var(--text-sm);color:var(--clr-text-muted)"><?= Helpers::h($r['desc']) ?></div>
        </div>
      </div>
    </div>
  </a>
  <?php endforeach; ?>
</div>
<?php endforeach; ?>

<?php
$pageContent = ob_get_clean();
$pageTitle   = 'Reports';
$activeMenu  = 'reports';
$breadcrumbs = [['label'=>'Reports']];
include CLMS_ROOT . '/templates/base.html.php';
