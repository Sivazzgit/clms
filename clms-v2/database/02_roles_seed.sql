-- ============================================================
-- CLMS 2.0 — Seed: Roles & Default Data
-- ============================================================

-- Roles
INSERT INTO `roles` (`code`, `name`, `description`) VALUES
('hr_admin',          'HR Admin',                  'Full system access — vendor management, approvals, all masters'),
('contractor',        'Contractor',                'Vendor portal — manage own employees and respond to indent requests'),
('section_incharge',  'Section In-charge',         'Raise indents, review deployments for assigned sections only'),
('hod',               'HOD',                       'Review and recommend indents and deployments'),
('plant_head',        'Plant Head / Factory Mgr',  'Final approval authority for indents and deployments'),
('gate_staff',        'Gate / Security Staff',     'Record daily shift-wise attendance at the gate');

-- Default Company (update before go-live)
INSERT INTO `companies` (`code`, `name`, `city`, `state`, `country`, `financial_year_start_month`) VALUES
('MBCPL', 'Muthiah Beverage and Confectionery (Pvt) Ltd', 'Bareilly', 'Uttar Pradesh', 'India', 4);

-- Default HR Admin user (password: Admin@1234 — MUST change on first login)
-- password_hash generated with PHP password_hash('Admin@1234', PASSWORD_BCRYPT)
INSERT INTO `users` (`company_id`, `username`, `password_hash`, `full_name`, `email`, `force_pwd_change`) VALUES
(1, 'clmsadmin', '$2y$12$YourHashHere', 'HR Administrator', 'admin@company.com', 1);

INSERT INTO `user_roles` (`user_id`, `role_id`, `assigned_by`) VALUES (1, 1, 1);
