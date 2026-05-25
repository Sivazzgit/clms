<?php
/**
 * CLMS 2.0 — index.php
 * Single entry point. All requests are routed here via .htaccess.
 */

define('CLMS_ROOT', __DIR__);

// ----------------------------------------------------------------
// Bootstrap
// ----------------------------------------------------------------
require CLMS_ROOT . '/config/app.php';
require CLMS_ROOT . '/core/DB.php';
require CLMS_ROOT . '/core/AuditLogger.php';
require CLMS_ROOT . '/core/Auth.php';
require CLMS_ROOT . '/core/Helpers.php';
require CLMS_ROOT . '/core/Router.php';

Auth::start();

// ----------------------------------------------------------------
// Routes
// ----------------------------------------------------------------
Router::any('/login',                              'modules/auth/login.php');
Router::any('/logout',                             'modules/auth/logout.php');
Router::any('/change-password',                    'modules/auth/change-password.php');

Router::any('/dashboard',                          'modules/dashboard/index.php');

// Users
Router::get('/users',                              'modules/users/index.php');
Router::any('/users/create',                       'modules/users/form.php');
Router::any('/users/{id:\d+}/edit',                'modules/users/form.php');
Router::post('/users/{id:\d+}/delete',             'modules/users/actions.php');
Router::post('/users/{id:\d+}/toggle-block',       'modules/users/actions.php');

// Vendors / Contractors
Router::get('/vendors',                            'modules/vendors/index.php');
Router::any('/vendors/create',                     'modules/vendors/form.php');
Router::any('/vendors/{id:\d+}/edit',              'modules/vendors/form.php');
Router::post('/vendors/actions',                   'modules/vendors/actions.php');
Router::post('/vendors/{id:\d+}/delete',           'modules/vendors/actions.php');
Router::any('/vendors/documents',                  'modules/vendors/documents.php');

// Employees
Router::get('/employees',                          'modules/employees/index.php');
Router::any('/employees/create',                   'modules/employees/form.php');
Router::any('/employees/{id:\d+}/edit',            'modules/employees/form.php');
Router::post('/employees/actions',                 'modules/employees/actions.php');
Router::post('/employees/{id:\d+}/delete',         'modules/employees/actions.php');
Router::post('/employees/{id:\d+}/approve',        'modules/employees/actions.php');
Router::any('/employees/pending',                  'modules/employees/pending.php');
Router::any('/employees/{id:\d+}/separate',        'modules/employees/separations.php');
Router::any('/employees/separations',              'modules/employees/separations.php');
Router::any('/employees/upload',                   'modules/employees/upload.php');

// Masters
Router::any('/masters/company',                    'modules/masters/company.php');
Router::any('/masters/shifts',                     'modules/masters/shifts.php');
Router::any('/masters/sections',                   'modules/masters/sections.php');
Router::any('/masters/categories',                 'modules/masters/categories.php');
Router::any('/masters/wage-rates',                 'modules/masters/wage_rates.php');
Router::any('/masters/holidays',                   'modules/masters/holidays.php');

// Indent
Router::get('/indent',                             'modules/indent/index.php');
Router::any('/indent/create',                      'modules/indent/form.php');
Router::any('/indent/{id:\d+}/edit',               'modules/indent/form.php');
Router::any('/indent/{id:\d+}/view',               'modules/indent/view.php');
Router::any('/indent/approve',                     'modules/indent/approve.php');
Router::any('/indent/final-approve',               'modules/indent/final_approve.php');
Router::any('/indent/assign',                      'modules/indent/assign.php');
Router::post('/indent/action',                     'modules/indent/actions.php');

// Deployment
Router::get('/deployment',                         'modules/deployment/index.php');
Router::any('/deployment/create',                  'modules/deployment/form.php');
Router::any('/deployment/{id:\d+}/edit',           'modules/deployment/form.php');
Router::any('/deployment/{id:\d+}/view',           'modules/deployment/view.php');
Router::any('/deployment/review',                  'modules/deployment/review.php');
Router::any('/deployment/approve',                 'modules/deployment/approve.php');
Router::any('/deployment/final',                   'modules/deployment/final.php');
Router::post('/deployment/action',                 'modules/deployment/actions.php');

// Attendance
Router::any('/attendance/gate',                    'modules/attendance/gate.php');
Router::any('/attendance/biometric',               'modules/attendance/biometric.php');
Router::get('/attendance',                         'modules/attendance/index.php');

// Billing
Router::get('/billing',                            'modules/billing/index.php');
Router::any('/billing/generate',                   'modules/billing/generate.php');
Router::any('/billing/{id:\d+}/view',              'modules/billing/view.php');
Router::any('/billing/{id:\d+}/payment',           'modules/billing/payments.php');
Router::post('/billing/action',                    'modules/billing/actions.php');

// Reports
Router::get('/reports/muster-roll',                'modules/reports/muster_roll.php');
Router::get('/reports/wage-register',              'modules/reports/wage_register.php');
Router::get('/reports/pf-esi',                     'modules/reports/pf_esi.php');
Router::get('/reports/form12a',                    'modules/reports/form12a.php');
Router::get('/reports/adult-workers',              'modules/reports/adult_workers.php');
Router::get('/reports/daily-manpower',             'modules/reports/daily_manpower.php');
Router::get('/reports/indent-deployment',          'modules/reports/indent_deployment.php');
Router::get('/reports/cost-contractor',            'modules/reports/cost_contractor.php');
Router::get('/reports/cost-section',               'modules/reports/cost_section.php');
Router::get('/reports/absenteeism',                'modules/reports/absenteeism.php');
Router::get('/reports/daily-cost',                 'modules/reports/daily_cost.php');
Router::get('/reports',                            'modules/reports/index.php');

// Audit
Router::get('/audit',                              'modules/audit/index.php');

// Impersonation
Router::any('/impersonate',                        'modules/impersonate/index.php');
Router::post('/impersonate/exit',                  'modules/impersonate/exit.php');

// Super Admin
Router::get('/superadmin',                         'modules/superadmin/index.php');
Router::get('/superadmin/companies',               'modules/superadmin/index.php');
Router::any('/superadmin/switch-company',          'modules/superadmin/switch_company.php');
Router::get('/superadmin/impersonation-log',       'modules/superadmin/impersonation_log.php');

// Root → handled directly in dispatch below
// Router::get('/', ...) intentionally omitted — root redirect is below

// ----------------------------------------------------------------
// Dispatch
// ----------------------------------------------------------------
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$_base = APP_BASE;
if ($_base !== '' && str_starts_with($uri, $_base)) {
    $uri = substr($uri, strlen($_base));
}
$uri = rtrim($uri, '/') ?: '/';
if ($uri === '/') {
    header('Location: ' . APP_BASE . (Auth::check() ? '/dashboard' : '/login'));
    exit;
}

Router::dispatch();
