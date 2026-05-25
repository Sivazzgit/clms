-- ============================================================
-- CLMS 2.0 — Seed: Default Master Data
-- Run after 01_schema.sql and 02_roles_seed.sql
-- ============================================================

-- Default Shifts (company_id = 1)
INSERT INTO `shifts` (`company_id`, `code`, `name`, `start_time`, `end_time`, `crosses_midnight`, `duration_hours`, `is_night_shift`) VALUES
(1, 'G', 'General Shift',  '09:00:00', '18:00:00', 0, 9.00, 0),
(1, 'A', 'Morning Shift',  '06:00:00', '14:00:00', 0, 8.00, 0),
(1, 'B', 'Afternoon Shift','14:00:00', '22:00:00', 0, 8.00, 0),
(1, 'C', 'Night Shift',    '22:00:00', '06:00:00', 1, 8.00, 1);

-- Default Labour Categories
INSERT INTO `labour_categories` (`company_id`, `code`, `name`, `description`) VALUES
(1, 'SUPVR',  'Supervisor',    'Supervisory grade workers'),
(1, 'SKILL',  'Skilled',       'Skilled trade workers'),
(1, 'SEMI',   'Semi-Skilled',  'Semi-skilled workers'),
(1, 'UNSK',   'Unskilled',     'Unskilled / helper grade workers'),
(1, 'ITI',    'ITI Trade',     'ITI certificate holders'),
(1, 'HLPR',   'Helper',        'General helpers');

-- Default Wage Components
INSERT INTO `wage_components` (`company_id`, `code`, `name`, `type`, `calc_method`, `is_statutory`, `sort_order`) VALUES
(1, 'BASIC',    'Basic Wage',              'earning',               'fixed',             0, 10),
(1, 'DA',       'Dearness Allowance',      'earning',               'percent_of_basic',  0, 20),
(1, 'HRA',      'House Rent Allowance',    'earning',               'percent_of_basic',  0, 30),
(1, 'UNIFORM',  'Uniform Allowance',       'earning',               'fixed',             0, 40),
(1, 'WASHING',  'Washing Allowance',       'earning',               'fixed',             0, 50),
(1, 'NIGHTSH',  'Night Shift Allowance',   'earning',               'fixed',             0, 60),
(1, 'SAFETYSH', 'Safety Shoe Allowance',   'earning',               'fixed',             0, 70),
(1, 'PF_EMP',   'PF (Employer Share)',     'employer_contribution', 'percent_of_basic',  1, 80),
(1, 'ESI_EMP',  'ESI (Employer Share)',    'employer_contribution', 'percent_of_gross',  1, 90),
(1, 'BONUS',    'Bonus',                   'earning',               'percent_of_basic',  0, 100),
(1, 'LEAVEENC', 'Leave Encashment',        'earning',               'fixed',             0, 110),
(1, 'SVCCHG',   'Service Charge',          'billing',               'percent_of_basic',  0, 120),
(1, 'MATCOST',  'Material Cost',           'billing',               'fixed',             0, 130),
(1, 'CGST',     'CGST',                    'billing',               'percent_of_gross',  0, 140),
(1, 'SGST',     'SGST',                    'billing',               'percent_of_gross',  0, 150),
(1, 'OTHERS',   'Others',                  'earning',               'fixed',             0, 160);
