<?php
/**
 * CLMS 2.0 — roles.php
 * Role definitions, permissions, and navigation menus per role.
 */

return [

    // ----------------------------------------------------------------
    // ROLE DEFINITIONS
    // code => [ label, description ]
    // ----------------------------------------------------------------
    'roles' => [
        'super_admin'      => ['label' => 'Super Admin',              'desc' => 'CLMS platform owner — cross-tenant, can impersonate anyone'],
        'admin'            => ['label' => 'Company Admin',           'desc' => 'Company-level admin — can impersonate non-admin users'],
        'hr_admin'         => ['label' => 'HR Admin',                'desc' => 'Full system access'],
        'contractor'       => ['label' => 'Contractor',              'desc' => 'Vendor portal'],
        'section_incharge' => ['label' => 'Section In-charge',       'desc' => 'Indent creation, deployment review'],
        'hod'              => ['label' => 'HOD',                     'desc' => 'Review and approve'],
        'plant_head'       => ['label' => 'Plant Head',              'desc' => 'Final approval authority'],
        'gate_staff'       => ['label' => 'Gate / Security Staff',   'desc' => 'Attendance entry'],
    ],

    // ----------------------------------------------------------------
    // IMPERSONATION: who can impersonate whom
    // ----------------------------------------------------------------
    'impersonation' => [
        // super_admin can impersonate any role in any company
        'super_admin' => ['*'],
        // admin can impersonate any non-privileged role within own company
        'admin'       => ['hr_admin', 'contractor', 'section_incharge', 'hod', 'plant_head', 'gate_staff'],
    ],

    // ----------------------------------------------------------------
    // SIDEBAR MENUS PER ROLE
    // Each item: [ label, url, icon_name, submenu? ]
    // icon_name matches SVG sprites in templates/icons.svg
    // ----------------------------------------------------------------
    'menus' => [

        'hr_admin' => [
            ['label' => 'Dashboard',       'url' => '/dashboard',           'icon' => 'home'],
            ['label' => 'Masters',         'url' => '#',                    'icon' => 'settings', 'submenu' => [
                ['label' => 'Company Setup',       'url' => '/masters/company'],
                ['label' => 'Shifts',              'url' => '/masters/shifts'],
                ['label' => 'Sections & Cost Centers', 'url' => '/masters/sections'],
                ['label' => 'Labour Categories',   'url' => '/masters/categories'],
                ['label' => 'Wage Components',     'url' => '/masters/wage-rates'],
                ['label' => 'Holiday Calendar',    'url' => '/masters/holidays'],
            ]],
            ['label' => 'Users',           'url' => '#',                    'icon' => 'users', 'submenu' => [
                ['label' => 'All Users',           'url' => '/users'],
                ['label' => 'Add User',            'url' => '/users/create'],
            ]],
            ['label' => 'Contractors',     'url' => '#',                    'icon' => 'briefcase', 'submenu' => [
                ['label' => 'Contractor List',     'url' => '/vendors'],
                ['label' => 'Add Contractor',      'url' => '/vendors/create'],
                ['label' => 'Documents',           'url' => '/vendors/documents'],
            ]],
            ['label' => 'Employees',       'url' => '#',                    'icon' => 'id-card', 'submenu' => [
                ['label' => 'All Employees',       'url' => '/employees'],
                ['label' => 'Pending Approvals',   'url' => '/employees/pending'],
                ['label' => 'Separations',         'url' => '/employees/separations'],
                ['label' => 'Bulk Upload',         'url' => '/employees/upload'],
            ]],
            ['label' => 'Indent',          'url' => '#',                    'icon' => 'clipboard', 'submenu' => [
                ['label' => 'All Indents',         'url' => '/indent'],
                ['label' => 'Vendor Assignment',   'url' => '/indent/assign'],
            ]],
            ['label' => 'Deployment',      'url' => '#',                    'icon' => 'users-check', 'submenu' => [
                ['label' => 'Daily View',          'url' => '/deployment'],
                ['label' => 'Approval Queue',      'url' => '/deployment/approve'],
            ]],
            ['label' => 'Attendance',      'url' => '#',                    'icon' => 'calendar-check', 'submenu' => [
                ['label' => 'Gate Entry',          'url' => '/attendance/gate'],
                ['label' => 'Biometric Upload',    'url' => '/attendance/biometric'],
                ['label' => 'Attendance View',     'url' => '/attendance'],
            ]],
            ['label' => 'Billing',         'url' => '#',                    'icon' => 'file-invoice', 'submenu' => [
                ['label' => 'Generate Bill',       'url' => '/billing/generate'],
                ['label' => 'Bill List',           'url' => '/billing'],
                ['label' => 'Payments',            'url' => '/billing/payments'],
            ]],
            ['label' => 'Statutory',       'url' => '#',                    'icon' => 'landmark', 'submenu' => [
                ['label' => 'Muster Roll (Form XII)',      'url' => '/reports/muster-roll'],
                ['label' => 'Wage Register (Form XIII)',   'url' => '/reports/wage-register'],
                ['label' => 'PF / ESI Statement',         'url' => '/reports/pf-esi'],
                ['label' => 'Form 12A (PF)',               'url' => '/reports/form12a'],
                ['label' => 'Adult Workers Register',      'url' => '/reports/adult-workers'],
            ]],
            ['label' => 'Reports',         'url' => '#',                    'icon' => 'bar-chart', 'submenu' => [
                ['label' => 'Daily Manpower Summary',      'url' => '/reports/daily-manpower'],
                ['label' => 'Indent vs. Deployment',       'url' => '/reports/indent-deployment'],
                ['label' => 'Contractor-wise Cost',        'url' => '/reports/cost-contractor'],
                ['label' => 'Section-wise Cost',           'url' => '/reports/cost-section'],
                ['label' => 'Absenteeism Report',          'url' => '/reports/absenteeism'],
            ]],
            ['label' => 'Audit Log',       'url' => '/audit',               'icon' => 'shield'],
        ],

        'contractor' => [
            ['label' => 'Dashboard',       'url' => '/dashboard',           'icon' => 'home'],
            ['label' => 'My Employees',    'url' => '#',                    'icon' => 'id-card', 'submenu' => [
                ['label' => 'Employee List',       'url' => '/employees'],
                ['label' => 'Add Employee',        'url' => '/employees/create'],
            ]],
            ['label' => 'Indent Requests', 'url' => '/indent',              'icon' => 'clipboard'],
            ['label' => 'Deployment',      'url' => '#',                    'icon' => 'users-check', 'submenu' => [
                ['label' => 'Submit Plan',         'url' => '/deployment/create'],
                ['label' => 'Deployment History',  'url' => '/deployment'],
            ]],
            ['label' => 'Attendance',      'url' => '/attendance',          'icon' => 'calendar-check'],
            ['label' => 'My Bills',        'url' => '/billing',             'icon' => 'file-invoice'],
        ],

        'section_incharge' => [
            ['label' => 'Dashboard',       'url' => '/dashboard',           'icon' => 'home'],
            ['label' => 'Indent',          'url' => '#',                    'icon' => 'clipboard', 'submenu' => [
                ['label' => 'My Indents',          'url' => '/indent'],
                ['label' => 'Create Indent',       'url' => '/indent/create'],
            ]],
            ['label' => 'Deployment',      'url' => '#',                    'icon' => 'users-check', 'submenu' => [
                ['label' => 'Review Deployment',   'url' => '/deployment/review'],
                ['label' => 'Gate Entry',          'url' => '/attendance/gate'],
            ]],
            ['label' => 'Attendance',      'url' => '/attendance',          'icon' => 'calendar-check'],
            ['label' => 'Reports',         'url' => '/reports',             'icon' => 'bar-chart'],
        ],

        'hod' => [
            ['label' => 'Dashboard',       'url' => '/dashboard',           'icon' => 'home'],
            ['label' => 'Indent Approvals','url' => '/indent/approve',      'icon' => 'clipboard'],
            ['label' => 'Deployment',      'url' => '/deployment/approve',  'icon' => 'users-check'],
            ['label' => 'Reports',         'url' => '/reports',             'icon' => 'bar-chart'],
        ],

        'plant_head' => [
            ['label' => 'Dashboard',       'url' => '/dashboard',           'icon' => 'home'],
            ['label' => 'Indent Approvals','url' => '/indent/final-approve','icon' => 'clipboard'],
            ['label' => 'Deployment Final','url' => '/deployment/final',    'icon' => 'users-check'],
            ['label' => 'Daily Cost Report','url' => '/reports/daily-cost', 'icon' => 'bar-chart'],
        ],

        'gate_staff' => [
            ['label' => 'Dashboard',       'url' => '/dashboard',           'icon' => 'home'],
            ['label' => 'Gate Entry',      'url' => '/attendance/gate',     'icon' => 'calendar-check'],
            ['label' => 'Attendance View', 'url' => '/attendance',          'icon' => 'eye'],
        ],

        'super_admin' => [
            ['label' => 'Platform',        'url' => '/superadmin',          'icon' => 'shield'],
            ['label' => 'All Companies',   'url' => '/superadmin/companies','icon' => 'briefcase'],
            ['label' => 'Switch Company',  'url' => '/superadmin/switch-company', 'icon' => 'users'],
            ['label' => 'Switch User',     'url' => '/impersonate',         'icon' => 'eye'],
            ['label' => 'Impersonation Log','url' => '/superadmin/impersonation-log', 'icon' => 'shield'],
        ],

        'admin' => [
            ['label' => 'Dashboard',       'url' => '/dashboard',           'icon' => 'home'],
            ['label' => 'Users',           'url' => '/users',               'icon' => 'users'],
            ['label' => 'Switch User',     'url' => '/impersonate',         'icon' => 'eye'],
            ['label' => 'Impersonation Log','url' => '/superadmin/impersonation-log','icon' => 'shield'],
        ],
    ],

    // ----------------------------------------------------------------
    // MODULE ACCESS MAP: which roles can access which module paths
    // ----------------------------------------------------------------
    'access' => [
        'dashboard'          => ['hr_admin', 'contractor', 'section_incharge', 'hod', 'plant_head', 'gate_staff'],
        'users'              => ['hr_admin'],
        'vendors'            => ['hr_admin'],
        'employees'          => ['hr_admin', 'contractor'],
        'employees.approve'  => ['hr_admin'],
        'masters'            => ['hr_admin'],
        'indent'             => ['hr_admin', 'section_incharge'],
        'indent.hod'         => ['hod'],
        'indent.plant'       => ['plant_head'],
        'indent.assign'      => ['hr_admin'],
        'indent.contractor'  => ['contractor'],
        'deployment'         => ['contractor', 'section_incharge', 'hod', 'plant_head', 'hr_admin'],
        'attendance'         => ['hr_admin', 'gate_staff', 'section_incharge'],
        'attendance.view'    => ['hr_admin', 'contractor', 'section_incharge'],
        'billing'            => ['hr_admin'],
        'reports'            => ['hr_admin', 'section_incharge', 'hod', 'plant_head'],
        'statutory'          => ['hr_admin'],
        'audit'              => ['hr_admin', 'super_admin', 'admin'],
        'impersonate'        => ['super_admin', 'admin'],
        'superadmin'         => ['super_admin'],
    ],
];
