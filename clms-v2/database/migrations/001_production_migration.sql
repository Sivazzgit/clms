-- =============================================================================
-- CLMS v2 — Production Migration Guide
-- =============================================================================
-- This file documents all schema changes between CLMS legacy (v1/MySQL 5.7)
-- and CLMS v2. Run the numbered sections in order on the production database.
--
-- Prerequisites:
--   1. Full backup of production DB before running anything.
--   2. Target DB must be MySQL 5.7+ or MariaDB 10.4+.
--   3. Run as a user with CREATE, ALTER, INSERT, DROP privileges.
--
-- Usage:
--   mysql -u root -p clms_v2 < 001_production_migration.sql
-- =============================================================================

-- Ensure we are in the correct DB
-- USE clms_v2;

-- =============================================================================
-- SECTION 1: Create New Database (if migrating to a fresh schema)
-- =============================================================================
-- If starting fresh on production, run 01_schema.sql first:
--   mysql -u root -p < clms-v2/database/01_schema.sql
--   mysql -u root -p clms_v2 < clms-v2/database/02_roles_seed.sql
--   mysql -u root -p clms_v2 < clms-v2/database/03_defaults_seed.sql
-- Then proceed to SECTION 3 for any incremental changes.

-- =============================================================================
-- SECTION 2: Column Renames (v1 → v2 mapping)
-- These were discovered during development. Apply only if migrating an existing
-- v1 schema with live data.
-- =============================================================================

-- 2.1 roles table: 'label' renamed to 'name'
--   v1: roles.label
--   v2: roles.name
-- ALTER TABLE roles CHANGE COLUMN `label` `name` VARCHAR(100) NOT NULL;

-- 2.2 vendors table: 'is_active' replaced by status enum
--   v1: vendors.is_active TINYINT(1)
--   v2: vendors.status ENUM('pending','active','suspended','expired') DEFAULT 'pending'
-- ALTER TABLE vendors
--   ADD COLUMN status ENUM('pending','active','suspended','expired') NOT NULL DEFAULT 'pending' AFTER name;
-- UPDATE vendors SET status='active' WHERE is_active=1;
-- UPDATE vendors SET status='suspended' WHERE is_active=0;
-- ALTER TABLE vendors DROP COLUMN is_active;

-- 2.3 attendance table: 'date' renamed to 'attendance_date'
--   v1: attendance.date DATE
--   v2: attendance.attendance_date DATE
-- ALTER TABLE attendance CHANGE COLUMN `date` `attendance_date` DATE NOT NULL;

-- 2.4 attendance table: status ENUM replaced by boolean flags
--   v1: attendance.status ENUM('present','absent','half_day','holiday')
--   v2: attendance.is_present TINYINT, is_absent TINYINT, is_half_day TINYINT,
--       is_overtime TINYINT, is_holiday TINYINT
-- ALTER TABLE attendance
--   ADD COLUMN is_present   TINYINT(1) NOT NULL DEFAULT 0,
--   ADD COLUMN is_absent    TINYINT(1) NOT NULL DEFAULT 0,
--   ADD COLUMN is_half_day  TINYINT(1) NOT NULL DEFAULT 0,
--   ADD COLUMN is_overtime  TINYINT(1) NOT NULL DEFAULT 0,
--   ADD COLUMN is_holiday   TINYINT(1) NOT NULL DEFAULT 0;
-- UPDATE attendance SET is_present=1 WHERE status='present';
-- UPDATE attendance SET is_absent=1  WHERE status='absent';
-- UPDATE attendance SET is_half_day=1, is_present=1 WHERE status='half_day';
-- UPDATE attendance SET is_holiday=1  WHERE status='holiday';
-- ALTER TABLE attendance DROP COLUMN status;

-- 2.5 indents table: 'assigned_to' column does not exist in v2
--   v2 uses indent_vendor_assignments table for vendor assignments.
--   status ENUM extended:
--   v1: ENUM('draft','submitted','approved','rejected','cancelled')
--   v2: ENUM('draft','submitted','hod_reviewed','plant_approved','hr_accepted',
--            'assigned','contractor_confirmed','rejected','cancelled')
-- ALTER TABLE indents
--   MODIFY COLUMN status ENUM(
--     'draft','submitted','hod_reviewed','plant_approved','hr_accepted',
--     'assigned','contractor_confirmed','rejected','cancelled'
--   ) NOT NULL DEFAULT 'draft';
-- Data migration for indents: map old 'approved' → 'plant_approved'
-- UPDATE indents SET status='plant_approved' WHERE status='approved';

-- =============================================================================
-- SECTION 3: New Tables in v2 (not in v1)
-- =============================================================================
-- These tables are defined in 01_schema.sql. If migrating FROM v1, create them:

-- 3.1 indent_approvals — multi-step approval log per indent
-- (see 01_schema.sql for full DDL)

-- 3.2 indent_vendor_assignments — HR assigns vendors to approved indents
-- (see 01_schema.sql for full DDL)

-- 3.3 deployment_plans — contractor submits deployment plan per indent+date
-- (see 01_schema.sql for full DDL)

-- 3.4 deployment_plan_employees — employees listed in a deployment plan
-- (see 01_schema.sql for full DDL)

-- 3.5 deployment_approvals — IC/HOD/PlantHead approval trail
-- (see 01_schema.sql for full DDL)

-- 3.6 billing_periods — replaces legacy invoice tables
-- (see 01_schema.sql for full DDL)

-- 3.7 billing_line_items — granular section × category × component billing
-- (see 01_schema.sql for full DDL)

-- 3.8 payments — payment receipts against billing periods
-- (see 01_schema.sql for full DDL)

-- 3.9 audit_log — full application audit trail
-- (see 01_schema.sql for full DDL)

-- 3.10 wage_components — configurable earning/deduction components
-- (see 01_schema.sql for full DDL)

-- 3.11 category_wage_rates — rate per category × component × date range
-- (see 01_schema.sql for full DDL)

-- 3.12 biometric_punches — raw biometric upload staging table
-- (see 01_schema.sql for full DDL)

-- 3.13 user_sections — maps users to allowed sections (section_incharge)
-- (see 01_schema.sql for full DDL)

-- 3.14 user_roles — maps users to roles (with optional vendor scope)
-- (see 01_schema.sql for full DDL)

-- =============================================================================
-- SECTION 4: New Columns on Existing Tables
-- =============================================================================

-- 4.1 employees: added category_id (was labour_type VARCHAR in v1)
-- ALTER TABLE employees ADD COLUMN category_id INT UNSIGNED NULL REFERENCES labour_categories(id);
-- (populate from existing labour_type data before dropping old column)

-- 4.2 employees: added shift_id
-- ALTER TABLE employees ADD COLUMN shift_id INT UNSIGNED NULL REFERENCES shifts(id);

-- 4.3 attendance: added vendor_id (denormalized for fast billing queries)
-- ALTER TABLE attendance ADD COLUMN vendor_id INT UNSIGNED NOT NULL REFERENCES vendors(id);
-- UPDATE attendance a JOIN employees e ON e.id=a.employee_id SET a.vendor_id=e.vendor_id;

-- 4.4 attendance: added source ENUM
-- ALTER TABLE attendance ADD COLUMN source ENUM('biometric','gate_manual','hr_manual') NOT NULL DEFAULT 'gate_manual';

-- 4.5 companies: added gstin, pan, financial_year_start_month (was in company_settings)
-- ALTER TABLE companies
--   ADD COLUMN gstin                     VARCHAR(20)  NULL,
--   ADD COLUMN pan                       VARCHAR(20)  NULL,
--   ADD COLUMN financial_year_start_month TINYINT     NOT NULL DEFAULT 4;

-- =============================================================================
-- SECTION 5: Seed Data
-- =============================================================================
-- If roles table is empty, seed from 02_roles_seed.sql
-- If wage_components / shifts / default data missing, seed from 03_defaults_seed.sql

-- 5.1 Admin user password column: v2 stores bcrypt hash in password_hash column
-- ALTER TABLE users ADD COLUMN password_hash VARCHAR(255) NULL AFTER password;
-- (application will re-hash on next login if both columns exist)

-- 5.2 users: force_pwd_change flag
-- ALTER TABLE users ADD COLUMN force_pwd_change TINYINT(1) NOT NULL DEFAULT 0;
-- UPDATE users SET force_pwd_change=1; -- Force all users to reset on first v2 login

-- =============================================================================
-- SECTION 6: Post-migration validation queries
-- =============================================================================
-- Run these to verify the migration succeeded:

-- SELECT COUNT(*) AS roles_count        FROM roles;                 -- should be ≥ 8
-- SELECT COUNT(*) AS companies_count    FROM companies;             -- should be ≥ 1
-- SELECT COUNT(*) AS shifts_count       FROM shifts;                -- should be ≥ 1
-- SELECT COUNT(*) AS employees_count    FROM employees;
-- SELECT COUNT(*) AS attendance_count   FROM attendance;
-- SELECT COUNT(*) AS missing_vendor_att FROM attendance WHERE vendor_id IS NULL;  -- should be 0
-- SELECT COUNT(*) AS missing_cat_emp    FROM employees WHERE category_id IS NULL; -- should be 0

-- =============================================================================
-- END OF MIGRATION GUIDE
-- =============================================================================
