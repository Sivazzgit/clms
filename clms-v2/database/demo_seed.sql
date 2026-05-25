-- =====================================================================
-- CLMS v2 — Demo / Customer Presentation Seed Data
-- Company : Muthiah Beverage and Confectionery (Pvt) Ltd  (id = 1)
-- Created : 2026-05-25
-- Demo user password : Demo@1234
-- =====================================================================

SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = '';

-- ----------------------------------------------------------------
-- 1. SHIFTS
-- ----------------------------------------------------------------
INSERT INTO shifts (id, company_id, code, name, start_time, end_time, duration_hours, crosses_midnight, is_night_shift, is_active)
VALUES
  (1, 1, 'MRN', 'Morning',  '06:00:00', '14:00:00', 8.00, 0, 0, 1),
  (2, 1, 'GEN', 'General',  '08:00:00', '17:00:00', 9.00, 0, 0, 1),
  (3, 1, 'EVN', 'Evening',  '14:00:00', '22:00:00', 8.00, 0, 0, 1)
ON DUPLICATE KEY UPDATE name=VALUES(name), start_time=VALUES(start_time), end_time=VALUES(end_time);

-- ----------------------------------------------------------------
-- 2. SECTIONS
-- ----------------------------------------------------------------
INSERT INTO sections (id, company_id, code, name, is_active, created_by)
VALUES
  (1, 1, 'PD-A',  'Production Line A', 1, 1),
  (2, 1, 'PD-B',  'Production Line B', 1, 1),
  (3, 1, 'PACK',  'Packaging',         1, 1),
  (4, 1, 'MAINT', 'Maintenance',       1, 1),
  (5, 1, 'WARE',  'Warehousing',       1, 1),
  (6, 1, 'QC',    'Quality Control',   1, 1)
ON DUPLICATE KEY UPDATE name=VALUES(name);

-- ----------------------------------------------------------------
-- 3. VENDORS  (activate existing + add two more)
-- ----------------------------------------------------------------
UPDATE vendors SET
  name                  = 'Kancor Labour Contractors',
  vendor_code           = 'CONT-A',
  city                  = 'Ernakulam',
  state                 = 'Kerala',
  contact_person        = 'Balan K',
  mobile                = '9876543210',
  pf_code               = 'KL/EKM/001234',
  esi_code              = '41001234567',
  labour_licence_no     = 'KL/LL/2024/001',
  labour_licence_expiry = '2027-03-31',
  contract_start_date   = '2024-04-01',
  contract_end_date     = '2027-03-31',
  max_employee_limit    = 50,
  status                = 'active',
  approved_by           = 1,
  approved_at           = NOW()
WHERE id = 1;

INSERT INTO vendors (id, company_id, vendor_code, name, city, state, contact_person, mobile, email,
  pf_code, esi_code, labour_licence_no, labour_licence_expiry,
  contract_start_date, contract_end_date, max_employee_limit,
  status, approved_by, approved_at, created_by)
VALUES
  (2, 1, 'CONT-B', 'Royal Manpower Services',  'Thrissur', 'Kerala',
   'Sreekumar P',  '9876500001', 'royal@example.com',
   'KL/TSR/002345', '41002345678', 'KL/LL/2024/002', '2027-03-31',
   '2024-04-01', '2027-03-31', 60, 'active', 1, NOW(), 1),
  (3, 1, 'CONT-C', 'Krishna Labour Solutions', 'Kochi',    'Kerala',
   'Krishnan R',   '9876500002', 'krishna@example.com',
   'KL/EKM/003456', '41003456789', 'KL/LL/2024/003', '2027-03-31',
   '2024-04-01', '2027-03-31', 40, 'active', 1, NOW(), 1)
ON DUPLICATE KEY UPDATE name=VALUES(name), status='active', approved_by=1, approved_at=NOW();

-- ----------------------------------------------------------------
-- 4. DEMO USERS  (password: Demo@1234)
-- ----------------------------------------------------------------
-- hash: $2y$10$wmal424.e6fPAy8AZ7xB6Ovz3oPTOfVNwjVFsKLB1yB39OkqwtUYa
INSERT INTO users (id, company_id, username, password_hash, full_name, email, mobile, is_active, force_pwd_change, created_by)
VALUES
  (3, 1, 'hr_manager',  '$2y$10$wmal424.e6fPAy8AZ7xB6Ovz3oPTOfVNwjVFsKLB1yB39OkqwtUYa', 'Priya Nair',         'hr@example.com',      '9000000003', 1, 0, 1),
  (4, 1, 'hod_prod',    '$2y$10$wmal424.e6fPAy8AZ7xB6Ovz3oPTOfVNwjVFsKLB1yB39OkqwtUYa', 'Rajan Menon',        'hod@example.com',     '9000000004', 1, 0, 1),
  (5, 1, 'plant_head',  '$2y$10$wmal424.e6fPAy8AZ7xB6Ovz3oPTOfVNwjVFsKLB1yB39OkqwtUYa', 'Suresh Pillai',      'plant@example.com',   '9000000005', 1, 0, 1),
  (6, 1, 'vendor_a',    '$2y$10$wmal424.e6fPAy8AZ7xB6Ovz3oPTOfVNwjVFsKLB1yB39OkqwtUYa', 'Balan K',            'balan@example.com',   '9876543210', 1, 0, 1),
  (7, 1, 'vendor_b',    '$2y$10$wmal424.e6fPAy8AZ7xB6Ovz3oPTOfVNwjVFsKLB1yB39OkqwtUYa', 'Sreekumar P',        'sree@example.com',    '9876500001', 1, 0, 1),
  (8, 1, 'vendor_c',    '$2y$10$wmal424.e6fPAy8AZ7xB6Ovz3oPTOfVNwjVFsKLB1yB39OkqwtUYa', 'Krishnan R',         'krishnan@example.com','9876500002', 1, 0, 1)
ON DUPLICATE KEY UPDATE full_name=VALUES(full_name);

-- User roles
-- role ids: 1=hr_admin, 2=contractor, 3=section_incharge, 4=hod, 5=plant_head, 6=gate_staff
INSERT INTO user_roles (user_id, role_id, vendor_id, assigned_by)
VALUES
  (3, 1, NULL, 1),   -- hr_manager  → hr_admin
  (4, 4, NULL, 1),   -- hod_prod    → hod
  (5, 5, NULL, 1),   -- plant_head  → plant_head
  (6, 2, 1,    1),   -- vendor_a    → contractor, vendor 1
  (7, 2, 2,    1),   -- vendor_b    → contractor, vendor 2
  (8, 2, 3,    1)    -- vendor_c    → contractor, vendor 3
ON DUPLICATE KEY UPDATE role_id=VALUES(role_id);

-- ----------------------------------------------------------------
-- 5. CATEGORY WAGE RATES  (effective 2024-04-01)
-- ----------------------------------------------------------------
-- component ids: 1=Basic, 2=DA, 3=HRA, 8=PF(employer), 9=ESI(employer)
INSERT INTO category_wage_rates (company_id, category_id, component_id, effective_from, amount, is_holiday_rate, is_ot_rate, created_by)
VALUES
-- Supervisor (cat 1)
  (1,1,1,'2024-04-01',  900.00,0,0,1),(1,1,2,'2024-04-01',  150.00,0,0,1),(1,1,3,'2024-04-01',  100.00,0,0,1),
  (1,1,8,'2024-04-01',  108.00,0,0,1),(1,1,9,'2024-04-01',   29.25,0,0,1),
-- Skilled (cat 2)
  (1,2,1,'2024-04-01',  720.00,0,0,1),(1,2,2,'2024-04-01',  120.00,0,0,1),(1,2,3,'2024-04-01',   80.00,0,0,1),
  (1,2,8,'2024-04-01',   86.40,0,0,1),(1,2,9,'2024-04-01',   23.40,0,0,1),
-- Semi-Skilled (cat 3)
  (1,3,1,'2024-04-01',  600.00,0,0,1),(1,3,2,'2024-04-01',  100.00,0,0,1),(1,3,3,'2024-04-01',   70.00,0,0,1),
  (1,3,8,'2024-04-01',   72.00,0,0,1),(1,3,9,'2024-04-01',   19.50,0,0,1),
-- Unskilled (cat 4)
  (1,4,1,'2024-04-01',  480.00,0,0,1),(1,4,2,'2024-04-01',   85.00,0,0,1),(1,4,3,'2024-04-01',   55.00,0,0,1),
  (1,4,8,'2024-04-01',   57.60,0,0,1),(1,4,9,'2024-04-01',   15.60,0,0,1),
-- ITI Trade (cat 5)
  (1,5,1,'2024-04-01',  750.00,0,0,1),(1,5,2,'2024-04-01',  125.00,0,0,1),(1,5,3,'2024-04-01',   85.00,0,0,1),
  (1,5,8,'2024-04-01',   90.00,0,0,1),(1,5,9,'2024-04-01',   24.38,0,0,1),
-- Helper (cat 6)
  (1,6,1,'2024-04-01',  440.00,0,0,1),(1,6,2,'2024-04-01',   80.00,0,0,1),(1,6,3,'2024-04-01',   50.00,0,0,1),
  (1,6,8,'2024-04-01',   52.80,0,0,1),(1,6,9,'2024-04-01',   14.30,0,0,1)
ON DUPLICATE KEY UPDATE amount=VALUES(amount);

-- ----------------------------------------------------------------
-- 6. EMPLOYEES
-- ----------------------------------------------------------------
-- VENDOR 1 — Kancor Labour Contractors (sections 1-2)
INSERT INTO employees (id, company_id, vendor_id, employee_code, first_name, last_name, gender, dob, aadhaar_no, pf_uan, esi_no, category_id, doj_vendor, doj_plant, status, approved_by, approved_at, added_by)
VALUES
  ( 1, 1, 1, 'CONT-A-001', 'Rajesh',       'Kumar',        'male',  '1990-05-12', '234500001000', 'UAN001234', 'ESI001234', 2, '2024-04-01','2024-04-01','active',1,NOW(),1),
  ( 2, 1, 1, 'CONT-A-002', 'Murugan',      'S',            'male',  '1988-08-22', '234500002000', 'UAN001235', 'ESI001235', 3, '2024-04-01','2024-04-01','active',1,NOW(),1),
  ( 3, 1, 1, 'CONT-A-003', 'Selvam',       'K',            'male',  '1995-03-10', '234500003000', 'UAN001236', 'ESI001236', 4, '2024-06-01','2024-06-01','active',1,NOW(),1),
  ( 4, 1, 1, 'CONT-A-004', 'Arumugam',     'P',            'male',  '1992-11-05', '234500004000', 'UAN001237', 'ESI001237', 2, '2024-04-01','2024-04-01','active',1,NOW(),1),
  ( 5, 1, 1, 'CONT-A-005', 'Venkatesan',   'R',            'male',  '1987-07-18', '234500005000', 'UAN001238', 'ESI001238', 3, '2024-04-01','2024-04-01','active',1,NOW(),1),
  ( 6, 1, 1, 'CONT-A-006', 'Palaniswamy',  'G',            'male',  '1998-01-25', '234500006000', 'UAN001239', 'ESI001239', 4, '2024-08-01','2024-08-01','active',1,NOW(),1),
  ( 7, 1, 1, 'CONT-A-007', 'Rajan',        'M',            'male',  '1993-09-14', '234500007000', 'UAN001240', 'ESI001240', 4, '2024-04-01','2024-04-01','active',1,NOW(),1),
  ( 8, 1, 1, 'CONT-A-008', 'Krishnamurthy','B',            'male',  '1985-12-30', '234500008000', 'UAN001241', 'ESI001241', 2, '2024-04-01','2024-04-01','active',1,NOW(),1),
-- VENDOR 2 — Royal Manpower Services (sections 3-4)
  ( 9, 1, 2, 'CONT-B-001', 'Suresh',       'Babu',         'male',  '1991-04-08', '234500009000', 'UAN002001', 'ESI002001', 2, '2024-05-01','2024-05-01','active',1,NOW(),1),
  (10, 1, 2, 'CONT-B-002', 'Annamalai',    'V',            'male',  '1989-06-20', '234500010000', 'UAN002002', 'ESI002002', 3, '2024-05-01','2024-05-01','active',1,NOW(),1),
  (11, 1, 2, 'CONT-B-003', 'Karuppusamy',  'T',            'male',  '1996-02-14', '234500011000', 'UAN002003', 'ESI002003', 4, '2024-05-01','2024-05-01','active',1,NOW(),1),
  (12, 1, 2, 'CONT-B-004', 'Muthu',        'S',            'male',  '1999-10-03', '234500012000', 'UAN002004', 'ESI002004', 6, '2024-07-01','2024-07-01','active',1,NOW(),1),
  (13, 1, 2, 'CONT-B-005', 'Ganesan',      'P',            'male',  '1994-03-27', '234500013000', 'UAN002005', 'ESI002005', 3, '2024-05-01','2024-05-01','active',1,NOW(),1),
  (14, 1, 2, 'CONT-B-006', 'Sundaram',     'K',            'male',  '1990-08-11', '234500014000', 'UAN002006', 'ESI002006', 4, '2024-05-01','2024-05-01','active',1,NOW(),1),
  (15, 1, 2, 'CONT-B-007', 'Perumal',      'A',            'male',  '1997-05-19', '234500015000', 'UAN002007', 'ESI002007', 6, '2024-09-01','2024-09-01','active',1,NOW(),1),
  (16, 1, 2, 'CONT-B-008', 'Mariappan',    'L',            'male',  '1986-12-07', '234500016000', 'UAN002008', 'ESI002008', 2, '2024-05-01','2024-05-01','active',1,NOW(),1),
-- VENDOR 3 — Krishna Labour Solutions (sections 5-6)
  (17, 1, 3, 'CONT-C-001', 'Subramanian',  'V',            'male',  '1982-07-15', '234500017000', 'UAN003001', 'ESI003001', 1, '2024-06-01','2024-06-01','active',1,NOW(),1),
  (18, 1, 3, 'CONT-C-002', 'Balasubramanian','R',          'male',  '1990-11-28', '234500018000', 'UAN003002', 'ESI003002', 5, '2024-06-01','2024-06-01','active',1,NOW(),1),
  (19, 1, 3, 'CONT-C-003', 'Chandrasekaran','M',           'male',  '1993-04-02', '234500019000', 'UAN003003', 'ESI003003', 2, '2024-06-01','2024-06-01','active',1,NOW(),1),
  (20, 1, 3, 'CONT-C-004', 'Vijayakumar',  'S',            'male',  '1995-09-16', '234500020000', 'UAN003004', 'ESI003004', 3, '2024-06-01','2024-06-01','active',1,NOW(),1),
  (21, 1, 3, 'CONT-C-005', 'Ramachandran', 'T',            'male',  '1988-02-23', '234500021000', 'UAN003005', 'ESI003005', 5, '2024-06-01','2024-06-01','active',1,NOW(),1),
  (22, 1, 3, 'CONT-C-006', 'Dinesh Kumar', 'P',            'male',  '1996-06-09', '234500022000', 'UAN003006', 'ESI003006', 2, '2024-06-01','2024-06-01','active',1,NOW(),1)
ON DUPLICATE KEY UPDATE status='active';

-- ----------------------------------------------------------------
-- 7. HOLIDAY
-- ----------------------------------------------------------------
INSERT INTO holidays (company_id, holiday_date, name, type, is_paid, created_by)
VALUES (1, '2026-05-01', 'Labour Day', 'national', 1, 1)
ON DUPLICATE KEY UPDATE name='Labour Day';

-- ----------------------------------------------------------------
-- 8. INDENTS
-- ----------------------------------------------------------------
-- Indent 1: Production Line A — fully approved & contractor confirmed
INSERT INTO indents (id, company_id, indent_no, section_id, start_date, end_date, indent_type, nature_of_work, status, created_by, created_at)
VALUES
  (1, 1, 'IND/2026/001', 1, '2026-04-01', '2026-06-30', 'range', 'Production activities for Spice Processing Line A', 'contractor_confirmed', 4, '2026-03-20 10:00:00'),
  (2, 1, 'IND/2026/002', 3, '2026-04-01', '2026-06-30', 'range', 'Packaging department — primary and secondary packaging', 'contractor_confirmed', 4, '2026-03-20 11:00:00'),
  (3, 1, 'IND/2026/003', 4, '2026-05-01', '2026-07-31', 'range', 'Maintenance tasks — preventive and corrective maintenance', 'hr_accepted',          4, '2026-04-10 09:00:00')
ON DUPLICATE KEY UPDATE status=VALUES(status);

-- Indent Lines
INSERT INTO indent_lines (id, indent_id, shift_id, category_id, required_count, preferred_vendor_id)
VALUES
  -- Indent 1: Production A (General shift)
  (1, 1, 2, 2, 4, 1),   -- 4 Skilled    → prefer Vendor 1
  (2, 1, 2, 4, 8, 1),   -- 8 Unskilled  → prefer Vendor 1
  -- Indent 2: Packaging (Morning shift)
  (3, 2, 1, 3, 3, 2),   -- 3 Semi-Skilled → prefer Vendor 2
  (4, 2, 1, 6, 5, 2),   -- 5 Helper       → prefer Vendor 2
  -- Indent 3: Maintenance (General shift)
  (5, 3, 2, 5, 2, 3),   -- 2 ITI Trade    → prefer Vendor 3
  (6, 3, 2, 2, 3, 3)    -- 3 Skilled      → prefer Vendor 3
ON DUPLICATE KEY UPDATE required_count=VALUES(required_count);

-- Indent Approvals (full chain for indents 1 & 2; partial for indent 3)
INSERT INTO indent_approvals (indent_id, step, role_code, action, actor_id, remarks, acted_at)
VALUES
  -- Indent 1
  (1,1,'hod',           'submitted',           4, 'Manpower required for Q1 production targets',   '2026-03-20 10:30:00'),
  (1,2,'hod',           'hod_reviewed',         4, 'Reviewed and forwarded to Plant Head',          '2026-03-21 09:00:00'),
  (1,3,'plant_head',    'plant_approved',        5, 'Approved — Production plan confirmed',          '2026-03-22 11:00:00'),
  (1,4,'hr_admin',      'hr_accepted',           3, 'Accepted — contractor assignment initiated',    '2026-03-23 10:00:00'),
  (1,5,'contractor',    'contractor_confirmed',  6, 'Confirmed — workers will be deployed',          '2026-03-24 09:00:00'),
  -- Indent 2
  (2,1,'hod',           'submitted',             4, 'Packaging line fully operational from April',   '2026-03-20 11:30:00'),
  (2,2,'hod',           'hod_reviewed',          4, 'Reviewed and approved at HOD level',            '2026-03-21 10:00:00'),
  (2,3,'plant_head',    'plant_approved',         5, 'Approved',                                      '2026-03-22 12:00:00'),
  (2,4,'hr_admin',      'hr_accepted',            3, 'Accepted',                                      '2026-03-23 11:00:00'),
  (2,5,'contractor',    'contractor_confirmed',   7, 'Confirmed — 8 workers to be deployed',          '2026-03-24 10:00:00'),
  -- Indent 3 (only up to hr_accepted)
  (3,1,'hod',           'submitted',              4, 'Maintenance manpower — urgent requirement',     '2026-04-10 09:30:00'),
  (3,2,'hod',           'hod_reviewed',           4, 'Reviewed',                                      '2026-04-11 09:00:00'),
  (3,3,'plant_head',    'plant_approved',          5, 'Approved — critical maintenance work',          '2026-04-12 10:00:00'),
  (3,4,'hr_admin',      'hr_accepted',             3, 'Accepted — awaiting contractor confirmation',   '2026-04-13 09:00:00');

-- Indent Vendor Assignments (for confirmed indents 1 & 2)
INSERT INTO indent_vendor_assignments (indent_id, indent_line_id, vendor_id, assigned_count, confirmed_count, status, assigned_by, assigned_at, confirmed_at)
VALUES
  (1, 1, 1, 4, 4, 'confirmed', 3, '2026-03-23 10:30:00', '2026-03-24 09:30:00'),
  (1, 2, 1, 8, 8, 'confirmed', 3, '2026-03-23 10:30:00', '2026-03-24 09:30:00'),
  (2, 3, 2, 3, 3, 'confirmed', 3, '2026-03-23 11:30:00', '2026-03-24 10:30:00'),
  (2, 4, 2, 5, 5, 'confirmed', 3, '2026-03-23 11:30:00', '2026-03-24 10:30:00');

-- ----------------------------------------------------------------
-- 9. ATTENDANCE  (Apr 27 – May 25, 2026 ; Mon-Sat present)
-- Sundays in range: Apr 26, May 3,10,17,24  → skipped
-- Labour Day May 1 → inserted as is_holiday=1
-- ----------------------------------------------------------------
DROP PROCEDURE IF EXISTS GenerateDemoAttendance;

DELIMITER //
CREATE PROCEDURE GenerateDemoAttendance()
BEGIN
  DECLARE v_date       DATE;
  DECLARE v_emp_id     INT;
  DECLARE v_vendor_id  INT;
  DECLARE v_section_id INT;
  DECLARE v_shift_id   INT;
  DECLARE v_is_holiday TINYINT;

  -- Employee → section & shift mapping
  CREATE TEMPORARY TABLE IF NOT EXISTS emp_section_map (
    emp_id     INT,
    vendor_id  INT,
    section_id INT,
    shift_id   INT
  );
  TRUNCATE TABLE emp_section_map;

  -- Vendor 1 employees: alternate between sections 1 and 2, General shift
  INSERT INTO emp_section_map VALUES
    ( 1,1,1,2),( 2,1,2,2),( 3,1,1,2),( 4,1,2,2),
    ( 5,1,1,2),( 6,1,2,2),( 7,1,1,2),( 8,1,2,2);
  -- Vendor 2 employees: sections 3-4, Morning shift
  INSERT INTO emp_section_map VALUES
    ( 9,2,3,1),(10,2,4,1),(11,2,3,1),(12,2,4,1),
    (13,2,3,1),(14,2,4,1),(15,2,3,1),(16,2,4,1);
  -- Vendor 3 employees: sections 5-6, Evening shift
  INSERT INTO emp_section_map VALUES
    (17,3,5,3),(18,3,6,3),(19,3,5,3),(20,3,6,3),
    (21,3,5,3),(22,3,6,3);

  SET v_date = '2026-04-27';

  WHILE v_date <= '2026-05-25' DO
    -- Skip Sundays
    IF DAYOFWEEK(v_date) != 1 THEN
      SET v_is_holiday = IF(v_date = '2026-05-01', 1, 0);

      INSERT INTO attendance
        (company_id, employee_id, vendor_id, section_id, attendance_date, shift_id,
         source, is_present, is_absent, is_holiday, worked_hours)
      SELECT
        1, m.emp_id, m.vendor_id, m.section_id, v_date, m.shift_id,
        'hr_manual',
        IF(v_is_holiday=1, 0, 1),  -- not present on holiday
        0,
        v_is_holiday,
        IF(v_is_holiday=1, 0.00,
          IF(m.shift_id=2, 9.00, 8.00))
      FROM emp_section_map m;
    END IF;
    SET v_date = DATE_ADD(v_date, INTERVAL 1 DAY);
  END WHILE;

  DROP TEMPORARY TABLE IF EXISTS emp_section_map;
END //
DELIMITER ;

CALL GenerateDemoAttendance();
DROP PROCEDURE IF EXISTS GenerateDemoAttendance;

-- Mark realistic absences (2-3 per employee spread across the month)
UPDATE attendance SET is_present=0, is_absent=1, worked_hours=0
WHERE (employee_id=1  AND attendance_date IN ('2026-04-29','2026-05-14'))
   OR (employee_id=2  AND attendance_date IN ('2026-05-06','2026-05-20'))
   OR (employee_id=3  AND attendance_date IN ('2026-04-30','2026-05-09'))
   OR (employee_id=4  AND attendance_date IN ('2026-05-02','2026-05-21'))
   OR (employee_id=5  AND attendance_date IN ('2026-05-05','2026-05-16'))
   OR (employee_id=6  AND attendance_date IN ('2026-04-28','2026-05-23'))
   OR (employee_id=7  AND attendance_date IN ('2026-05-07','2026-05-15'))
   OR (employee_id=8  AND attendance_date IN ('2026-05-12','2026-05-22'))
   OR (employee_id=9  AND attendance_date IN ('2026-04-29','2026-05-13'))
   OR (employee_id=10 AND attendance_date IN ('2026-05-08','2026-05-19'))
   OR (employee_id=11 AND attendance_date IN ('2026-05-02','2026-05-16'))
   OR (employee_id=12 AND attendance_date IN ('2026-05-06','2026-05-20','2026-05-25'))
   OR (employee_id=13 AND attendance_date IN ('2026-04-30','2026-05-14'))
   OR (employee_id=14 AND attendance_date IN ('2026-05-07','2026-05-21'))
   OR (employee_id=15 AND attendance_date IN ('2026-05-09','2026-05-23'))
   OR (employee_id=16 AND attendance_date IN ('2026-04-28','2026-05-12'))
   OR (employee_id=17 AND attendance_date IN ('2026-05-05','2026-05-19'))
   OR (employee_id=18 AND attendance_date IN ('2026-05-08','2026-05-22'))
   OR (employee_id=19 AND attendance_date IN ('2026-04-30','2026-05-15'))
   OR (employee_id=20 AND attendance_date IN ('2026-05-06','2026-05-20'))
   OR (employee_id=21 AND attendance_date IN ('2026-05-13','2026-05-25'))
   OR (employee_id=22 AND attendance_date IN ('2026-05-09','2026-05-23'));

-- ----------------------------------------------------------------
-- Done
-- ----------------------------------------------------------------
SET FOREIGN_KEY_CHECKS = 1;

SELECT 'Demo seed complete' AS status;
SELECT 'Shifts'    AS tbl, COUNT(*) AS cnt FROM shifts
UNION SELECT 'Sections',    COUNT(*) FROM sections
UNION SELECT 'Vendors',     COUNT(*) FROM vendors
UNION SELECT 'Users',       COUNT(*) FROM users
UNION SELECT 'Employees',   COUNT(*) FROM employees
UNION SELECT 'Indents',     COUNT(*) FROM indents
UNION SELECT 'Attendance',  COUNT(*) FROM attendance;
