-- ============================================================
-- CLMS 2.0 — Database Schema
-- MySQL 8.x
-- ============================================================

SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';
SET time_zone = '+05:30';

-- ============================================================
-- MODULE 1: FOUNDATION — Companies, Users, Roles, Auth
-- ============================================================

CREATE TABLE `companies` (
  `id`                        INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `code`                      VARCHAR(20)  NOT NULL,
  `name`                      VARCHAR(150) NOT NULL,
  `address`                   TEXT,
  `city`                      VARCHAR(100),
  `state`                     VARCHAR(100),
  `country`                   VARCHAR(100) DEFAULT 'India',
  `gstin`                     VARCHAR(20),
  `pan`                       VARCHAR(15),
  `logo_path`                 VARCHAR(255),
  `financial_year_start_month` TINYINT UNSIGNED DEFAULT 4 COMMENT '4 = April',
  `is_active`                 TINYINT(1) DEFAULT 1,
  `created_at`                DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at`                DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_company_code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------

CREATE TABLE `roles` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `code`        VARCHAR(30)  NOT NULL COMMENT 'hr_admin | contractor | section_incharge | hod | plant_head | gate_staff',
  `name`        VARCHAR(100) NOT NULL,
  `description` TEXT,
  `is_active`   TINYINT(1) DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_role_code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------

CREATE TABLE `users` (
  `id`                  INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id`          INT UNSIGNED NOT NULL,
  `username`            VARCHAR(50)  NOT NULL,
  `password_hash`       VARCHAR(255) NOT NULL,
  `full_name`           VARCHAR(150) NOT NULL,
  `email`               VARCHAR(150),
  `mobile`              VARCHAR(15),
  `employee_code`       VARCHAR(20)  COMMENT 'If the user is a permanent company employee',
  `is_active`           TINYINT(1) DEFAULT 1,
  `is_blocked`          TINYINT(1) DEFAULT 0,
  `force_pwd_change`    TINYINT(1) DEFAULT 1 COMMENT 'Must change password on first login',
  `last_login_at`       DATETIME,
  `password_changed_at` DATETIME,
  `created_by`          INT UNSIGNED,
  `created_at`          DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at`          DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_username` (`username`),
  KEY `fk_users_company` (`company_id`),
  CONSTRAINT `fk_users_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------
-- A user can hold multiple roles (e.g., HR Admin + HOD)

CREATE TABLE `user_roles` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`     INT UNSIGNED NOT NULL,
  `role_id`     INT UNSIGNED NOT NULL,
  `vendor_id`   INT UNSIGNED NULL COMMENT 'Filled only when role = contractor',
  `assigned_by` INT UNSIGNED,
  `assigned_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_user_role` (`user_id`, `role_id`),
  KEY `fk_ur_user` (`user_id`),
  KEY `fk_ur_role` (`role_id`),
  CONSTRAINT `fk_ur_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_ur_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------
-- Section in-charges are restricted to their assigned sections

CREATE TABLE `user_sections` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`     INT UNSIGNED NOT NULL,
  `section_id`  INT UNSIGNED NOT NULL,
  `is_primary`  TINYINT(1) DEFAULT 0,
  `assigned_by` INT UNSIGNED,
  `assigned_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_user_section` (`user_id`, `section_id`),
  KEY `fk_us_user` (`user_id`),
  CONSTRAINT `fk_us_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------

CREATE TABLE `user_sessions` (
  `session_token` VARCHAR(128) NOT NULL,
  `user_id`       INT UNSIGNED NOT NULL,
  `ip_address`    VARCHAR(45),
  `user_agent`    TEXT,
  `last_activity` DATETIME,
  `expires_at`    DATETIME,
  `created_at`    DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`session_token`),
  KEY `fk_sess_user` (`user_id`),
  CONSTRAINT `fk_sess_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------

CREATE TABLE `audit_log` (
  `id`          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id`  INT UNSIGNED,
  `user_id`     INT UNSIGNED,
  `username`    VARCHAR(50),
  `action`      VARCHAR(50)  COMMENT 'INSERT | UPDATE | DELETE | LOGIN | LOGOUT | APPROVE | REJECT',
  `module`      VARCHAR(100) COMMENT 'users | vendors | employees | indent | deployment | attendance',
  `record_id`   VARCHAR(50)  COMMENT 'Primary key of the affected record',
  `old_values`  JSON         COMMENT 'Previous field values (UPDATE / DELETE)',
  `new_values`  JSON         COMMENT 'New field values (INSERT / UPDATE)',
  `ip_address`  VARCHAR(45),
  `user_agent`  VARCHAR(500),
  `created_at`  DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_audit_user`    (`user_id`),
  KEY `idx_audit_module`  (`module`),
  KEY `idx_audit_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- MODULE 2: MASTER DATA
-- ============================================================

CREATE TABLE `cost_centers` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id` INT UNSIGNED NOT NULL,
  `code`       VARCHAR(30)  NOT NULL,
  `name`       VARCHAR(100) NOT NULL,
  `is_active`  TINYINT(1) DEFAULT 1,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_cc_code` (`company_id`, `code`),
  CONSTRAINT `fk_cc_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------

CREATE TABLE `sections` (
  `id`             INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id`     INT UNSIGNED NOT NULL,
  `code`           VARCHAR(30)  NOT NULL,
  `name`           VARCHAR(100) NOT NULL,
  `cost_center_id` INT UNSIGNED,
  `area`           VARCHAR(100),
  `is_active`      TINYINT(1) DEFAULT 1,
  `created_by`     INT UNSIGNED,
  `created_at`     DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at`     DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_section_code` (`company_id`, `code`),
  KEY `fk_sec_company`  (`company_id`),
  KEY `fk_sec_cc`       (`cost_center_id`),
  CONSTRAINT `fk_sec_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`),
  CONSTRAINT `fk_sec_cc`      FOREIGN KEY (`cost_center_id`) REFERENCES `cost_centers` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------

CREATE TABLE `shifts` (
  `id`               INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id`       INT UNSIGNED NOT NULL,
  `code`             VARCHAR(10)  NOT NULL COMMENT 'A | B | C | G | Night etc.',
  `name`             VARCHAR(50)  NOT NULL,
  `start_time`       TIME NOT NULL,
  `end_time`         TIME NOT NULL,
  `crosses_midnight` TINYINT(1) DEFAULT 0,
  `duration_hours`   DECIMAL(4,2),
  `is_night_shift`   TINYINT(1) DEFAULT 0,
  `is_active`        TINYINT(1) DEFAULT 1,
  `created_at`       DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_shift_code` (`company_id`, `code`),
  CONSTRAINT `fk_shift_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------

CREATE TABLE `holidays` (
  `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id`   INT UNSIGNED NOT NULL,
  `holiday_date` DATE NOT NULL,
  `name`         VARCHAR(150) NOT NULL,
  `type`         ENUM('national', 'state', 'factory', 'optional') DEFAULT 'factory',
  `is_paid`      TINYINT(1) DEFAULT 1,
  `created_by`   INT UNSIGNED,
  `created_at`   DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_holiday_date` (`company_id`, `holiday_date`),
  CONSTRAINT `fk_holiday_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------

CREATE TABLE `labour_categories` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id`  INT UNSIGNED NOT NULL,
  `code`        VARCHAR(20)  NOT NULL,
  `name`        VARCHAR(100) NOT NULL COMMENT 'Skilled | Semi-Skilled | Unskilled | ITI | Helper | Supervisor',
  `description` TEXT,
  `is_active`   TINYINT(1) DEFAULT 1,
  `created_at`  DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_cat_code` (`company_id`, `code`),
  CONSTRAINT `fk_cat_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------

CREATE TABLE `wage_components` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id`  INT UNSIGNED NOT NULL,
  `code`        VARCHAR(30)  NOT NULL,
  `name`        VARCHAR(100) NOT NULL,
  `type`        ENUM('earning', 'deduction', 'employer_contribution', 'billing') DEFAULT 'earning',
  `calc_method` ENUM('fixed', 'percent_of_basic', 'percent_of_gross', 'statutory') DEFAULT 'fixed',
  `is_statutory` TINYINT(1) DEFAULT 0 COMMENT 'PF, ESI, etc.',
  `sort_order`  INT DEFAULT 0,
  `is_active`   TINYINT(1) DEFAULT 1,
  `created_at`  DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_wcomp_code` (`company_id`, `code`),
  CONSTRAINT `fk_wcomp_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------
-- Rates per category per component, with effective date history

CREATE TABLE `category_wage_rates` (
  `id`             INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id`     INT UNSIGNED NOT NULL,
  `category_id`    INT UNSIGNED NOT NULL,
  `component_id`   INT UNSIGNED NOT NULL,
  `effective_from` DATE NOT NULL,
  `effective_to`   DATE COMMENT 'NULL means currently active',
  `amount`         DECIMAL(12,2) NOT NULL DEFAULT 0,
  `percent`        DECIMAL(6,4)  COMMENT 'Used when calc_method is percent_*',
  `is_holiday_rate` TINYINT(1) DEFAULT 0,
  `is_ot_rate`     TINYINT(1) DEFAULT 0,
  `created_by`     INT UNSIGNED,
  `created_at`     DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_cwr_cat`  (`category_id`),
  KEY `fk_cwr_comp` (`component_id`),
  CONSTRAINT `fk_cwr_cat`  FOREIGN KEY (`category_id`)  REFERENCES `labour_categories` (`id`),
  CONSTRAINT `fk_cwr_comp` FOREIGN KEY (`component_id`) REFERENCES `wage_components` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- MODULE 3: VENDOR MANAGEMENT
-- ============================================================

CREATE TABLE `vendors` (
  `id`                     INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id`             INT UNSIGNED NOT NULL,
  `code`                   VARCHAR(20)  NOT NULL COMMENT 'Auto-generated: VND-001',
  `name`                   VARCHAR(150) NOT NULL,
  `trade_name`             VARCHAR(150),
  `address`                TEXT,
  `city`                   VARCHAR(100),
  `state`                  VARCHAR(100),
  `pincode`                VARCHAR(10),
  `contact_person`         VARCHAR(100),
  `mobile`                 VARCHAR(15),
  `email`                  VARCHAR(100),
  `gstin`                  VARCHAR(20),
  `pan`                    VARCHAR(15),
  `esi_code`               VARCHAR(30),
  `pf_code`                VARCHAR(30),
  `labour_licence_no`      VARCHAR(50),
  `labour_licence_expiry`  DATE,
  `max_employee_limit`     INT UNSIGNED DEFAULT 0,
  `contract_start_date`    DATE,
  `contract_end_date`      DATE,
  `bank_name`              VARCHAR(100),
  `bank_account_no`        VARCHAR(30),
  `bank_ifsc`              VARCHAR(15),
  `bank_branch`            VARCHAR(100),
  `status`                 ENUM('pending', 'active', 'suspended', 'expired') DEFAULT 'pending',
  `approved_by`            INT UNSIGNED,
  `approved_at`            DATETIME,
  `created_by`             INT UNSIGNED NOT NULL,
  `created_at`             DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at`             DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_vendor_code` (`company_id`, `code`),
  CONSTRAINT `fk_vendor_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------

CREATE TABLE `vendor_documents` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `vendor_id`   INT UNSIGNED NOT NULL,
  `doc_type`    VARCHAR(50)  COMMENT 'gst_cert | pf_cert | esi_cert | labour_licence | pan_card | other',
  `doc_name`    VARCHAR(150) NOT NULL,
  `file_path`   VARCHAR(255) NOT NULL,
  `expiry_date` DATE,
  `uploaded_by` INT UNSIGNED,
  `uploaded_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_vdoc_vendor` (`vendor_id`),
  CONSTRAINT `fk_vdoc_vendor` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- MODULE 4: EMPLOYEE MASTER
-- ============================================================

CREATE TABLE `employees` (
  `id`                      INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id`              INT UNSIGNED NOT NULL,
  `vendor_id`               INT UNSIGNED NOT NULL,
  `employee_code`           VARCHAR(20)  NOT NULL COMMENT 'Auto-generated: EMP-VND001-001',
  `biometric_id`            VARCHAR(30)  COMMENT 'ID programmed in biometric device',
  `first_name`              VARCHAR(60)  NOT NULL,
  `middle_name`             VARCHAR(60),
  `last_name`               VARCHAR(60)  NOT NULL,
  `gender`                  ENUM('male', 'female', 'other'),
  `dob`                     DATE,
  `father_name`             VARCHAR(100),
  `permanent_address`       TEXT,
  `current_address`         TEXT,
  `mobile`                  VARCHAR(15),
  `emergency_contact_name`  VARCHAR(100),
  `emergency_contact_mobile` VARCHAR(15),
  `aadhaar_no`              VARCHAR(16),
  `pan_no`                  VARCHAR(15),
  `esi_no`                  VARCHAR(20),
  `pf_uan`                  VARCHAR(20),
  `bank_name`               VARCHAR(100),
  `bank_account_no`         VARCHAR(30),
  `bank_ifsc`               VARCHAR(15),
  `bank_branch`             VARCHAR(100),
  `category_id`             INT UNSIGNED  COMMENT 'Labour category',
  `qualification`           VARCHAR(100),
  `trade`                   VARCHAR(100),
  `doj_vendor`              DATE          COMMENT 'Date of joining with contractor',
  `doj_plant`               DATE          COMMENT 'Date of joining at this plant',
  `photo_path`              VARCHAR(255),
  `status`                  ENUM('pending_approval', 'active', 'inactive', 'separated') DEFAULT 'pending_approval',
  `approved_by`             INT UNSIGNED,
  `approved_at`             DATETIME,
  `added_by`                INT UNSIGNED NOT NULL,
  `created_at`              DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at`              DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_emp_code`    (`company_id`, `employee_code`),
  UNIQUE KEY `uq_emp_aadhaar` (`aadhaar_no`),
  KEY `fk_emp_vendor`   (`vendor_id`),
  KEY `fk_emp_category` (`category_id`),
  CONSTRAINT `fk_emp_company`  FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`),
  CONSTRAINT `fk_emp_vendor`   FOREIGN KEY (`vendor_id`)  REFERENCES `vendors` (`id`),
  CONSTRAINT `fk_emp_category` FOREIGN KEY (`category_id`) REFERENCES `labour_categories` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------

CREATE TABLE `employee_separations` (
  `id`                INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `employee_id`       INT UNSIGNED NOT NULL,
  `separation_date`   DATE NOT NULL,
  `reason`            ENUM('resignation', 'contract_completion', 'termination', 'absconding', 'death', 'other'),
  `reason_details`    TEXT,
  `last_working_date` DATE,
  `processed_by`      INT UNSIGNED,
  `processed_at`      DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_sep_emp` (`employee_id`),
  CONSTRAINT `fk_sep_emp` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- MODULE 5: MANPOWER INDENT
-- ============================================================

CREATE TABLE `indents` (
  `id`                 INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id`         INT UNSIGNED NOT NULL,
  `indent_no`          VARCHAR(30)  NOT NULL COMMENT 'IND-2526-0001',
  `section_id`         INT UNSIGNED NOT NULL,
  `start_date`         DATE NOT NULL,
  `end_date`           DATE NOT NULL,
  `indent_type`        ENUM('daily', 'monthly', 'range') DEFAULT 'range',
  `is_urgent`          TINYINT(1) DEFAULT 0,
  `is_recurring`       TINYINT(1) DEFAULT 0,
  `recurrence_pattern` VARCHAR(50)  COMMENT 'monthly | weekly',
  `nature_of_work`     TEXT,
  `remarks`            TEXT,
  `status`             ENUM('draft', 'submitted', 'hod_reviewed', 'plant_approved', 'hr_accepted', 'assigned', 'contractor_confirmed', 'rejected', 'cancelled') DEFAULT 'draft',
  `parent_indent_id`   INT UNSIGNED COMMENT 'For recurring indents',
  `created_by`         INT UNSIGNED NOT NULL,
  `created_at`         DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at`         DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_indent_no` (`company_id`, `indent_no`),
  KEY `fk_indent_section` (`section_id`),
  CONSTRAINT `fk_indent_company`  FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`),
  CONSTRAINT `fk_indent_section`  FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------

CREATE TABLE `indent_lines` (
  `id`                  INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `indent_id`           INT UNSIGNED NOT NULL,
  `shift_id`            INT UNSIGNED NOT NULL,
  `category_id`         INT UNSIGNED NOT NULL,
  `required_count`      INT UNSIGNED NOT NULL DEFAULT 1,
  `preferred_vendor_id` INT UNSIGNED,
  `remarks`             TEXT,
  PRIMARY KEY (`id`),
  KEY `fk_il_indent`   (`indent_id`),
  KEY `fk_il_shift`    (`shift_id`),
  KEY `fk_il_category` (`category_id`),
  CONSTRAINT `fk_il_indent`   FOREIGN KEY (`indent_id`)   REFERENCES `indents` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_il_shift`    FOREIGN KEY (`shift_id`)    REFERENCES `shifts` (`id`),
  CONSTRAINT `fk_il_category` FOREIGN KEY (`category_id`) REFERENCES `labour_categories` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------
-- Each approval step is one row: who acted, what they did, when

CREATE TABLE `indent_approvals` (
  `id`        INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `indent_id` INT UNSIGNED NOT NULL,
  `step`      TINYINT UNSIGNED NOT NULL COMMENT '1=IC Submit | 2=HOD | 3=Plant Head | 4=HR | 5=Contractor',
  `role_code` VARCHAR(30)  COMMENT 'Role that performed this step',
  `action`    ENUM('submitted', 'hod_reviewed', 'plant_approved', 'hr_accepted', 'contractor_confirmed', 'rejected', 'sent_back') NOT NULL,
  `actor_id`  INT UNSIGNED NOT NULL,
  `remarks`   TEXT,
  `acted_at`  DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_ia_indent` (`indent_id`),
  CONSTRAINT `fk_ia_indent` FOREIGN KEY (`indent_id`) REFERENCES `indents` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------

CREATE TABLE `indent_vendor_assignments` (
  `id`                INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `indent_id`         INT UNSIGNED NOT NULL,
  `indent_line_id`    INT UNSIGNED NOT NULL,
  `vendor_id`         INT UNSIGNED NOT NULL,
  `assigned_count`    INT UNSIGNED NOT NULL,
  `confirmed_count`   INT UNSIGNED,
  `status`            ENUM('assigned', 'confirmed', 'partially_confirmed', 'rejected') DEFAULT 'assigned',
  `assigned_by`       INT UNSIGNED,
  `assigned_at`       DATETIME DEFAULT CURRENT_TIMESTAMP,
  `confirmed_at`      DATETIME,
  PRIMARY KEY (`id`),
  KEY `fk_iva_indent` (`indent_id`),
  KEY `fk_iva_line`   (`indent_line_id`),
  KEY `fk_iva_vendor` (`vendor_id`),
  CONSTRAINT `fk_iva_indent`  FOREIGN KEY (`indent_id`)      REFERENCES `indents` (`id`),
  CONSTRAINT `fk_iva_line`    FOREIGN KEY (`indent_line_id`) REFERENCES `indent_lines` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_iva_vendor`  FOREIGN KEY (`vendor_id`)      REFERENCES `vendors` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- MODULE 6: MANPOWER DEPLOYMENT
-- ============================================================

CREATE TABLE `deployment_plans` (
  `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id`   INT UNSIGNED NOT NULL,
  `indent_id`    INT UNSIGNED NOT NULL,
  `vendor_id`    INT UNSIGNED NOT NULL,
  `plan_date`    DATE NOT NULL,
  `shift_id`     INT UNSIGNED NOT NULL,
  `section_id`   INT UNSIGNED NOT NULL,
  `status`       ENUM('submitted', 'ic_reviewed', 'hod_accepted', 'plant_approved', 'rejected') DEFAULT 'submitted',
  `submitted_by` INT UNSIGNED NOT NULL,
  `submitted_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at`   DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_dp_indent`  (`indent_id`),
  KEY `fk_dp_vendor`  (`vendor_id`),
  KEY `fk_dp_section` (`section_id`),
  KEY `idx_dp_date`   (`plan_date`),
  CONSTRAINT `fk_dp_company`  FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`),
  CONSTRAINT `fk_dp_indent`   FOREIGN KEY (`indent_id`)  REFERENCES `indents` (`id`),
  CONSTRAINT `fk_dp_vendor`   FOREIGN KEY (`vendor_id`)  REFERENCES `vendors` (`id`),
  CONSTRAINT `fk_dp_section`  FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`),
  CONSTRAINT `fk_dp_shift`    FOREIGN KEY (`shift_id`)   REFERENCES `shifts` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------

CREATE TABLE `deployment_plan_employees` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `plan_id`     INT UNSIGNED NOT NULL,
  `employee_id` INT UNSIGNED NOT NULL,
  `added_by`    INT UNSIGNED NOT NULL,
  `added_at`    DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_plan_emp` (`plan_id`, `employee_id`),
  KEY `fk_dpe_plan` (`plan_id`),
  KEY `fk_dpe_emp`  (`employee_id`),
  CONSTRAINT `fk_dpe_plan` FOREIGN KEY (`plan_id`)     REFERENCES `deployment_plans` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_dpe_emp`  FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------

CREATE TABLE `deployment_approvals` (
  `id`       INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `plan_id`  INT UNSIGNED NOT NULL,
  `step`     TINYINT UNSIGNED NOT NULL COMMENT '1=IC review | 2=HOD accept | 3=Plant Head approve',
  `action`   ENUM('reviewed', 'accepted', 'approved', 'rejected', 'sent_back', 'substituted') NOT NULL,
  `actor_id` INT UNSIGNED NOT NULL,
  `remarks`  TEXT,
  `acted_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_da_plan` (`plan_id`),
  CONSTRAINT `fk_da_plan` FOREIGN KEY (`plan_id`) REFERENCES `deployment_plans` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- MODULE 7: ATTENDANCE
-- ============================================================
-- Single unified table — no per-month fragmentation

CREATE TABLE `attendance` (
  `id`              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id`      INT UNSIGNED NOT NULL,
  `employee_id`     INT UNSIGNED NOT NULL,
  `vendor_id`       INT UNSIGNED NOT NULL,
  `section_id`      INT UNSIGNED NOT NULL,
  `attendance_date` DATE NOT NULL,
  `shift_id`        INT UNSIGNED NOT NULL,
  `source`          ENUM('biometric', 'gate_manual', 'hr_manual') DEFAULT 'gate_manual',
  `in_time`         DATETIME,
  `out_time`        DATETIME,
  `worked_hours`    DECIMAL(5,2),
  `is_present`      TINYINT(1) DEFAULT 0,
  `is_absent`       TINYINT(1) DEFAULT 0,
  `is_half_day`     TINYINT(1) DEFAULT 0,
  `is_overtime`     TINYINT(1) DEFAULT 0,
  `ot_hours`        DECIMAL(5,2) DEFAULT 0,
  `is_holiday`      TINYINT(1) DEFAULT 0,
  `exception_flag`  TINYINT(1) DEFAULT 0 COMMENT 'Late arrival | early departure | no punch',
  `exception_reason` VARCHAR(100),
  `approved_by`     INT UNSIGNED,
  `approved_at`     DATETIME,
  `created_at`      DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at`      DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_att` (`employee_id`, `attendance_date`, `shift_id`),
  KEY `idx_att_date`    (`attendance_date`),
  KEY `idx_att_vendor`  (`vendor_id`),
  KEY `idx_att_section` (`section_id`),
  CONSTRAINT `fk_att_company`  FOREIGN KEY (`company_id`)  REFERENCES `companies` (`id`),
  CONSTRAINT `fk_att_emp`      FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`),
  CONSTRAINT `fk_att_vendor`   FOREIGN KEY (`vendor_id`)   REFERENCES `vendors` (`id`),
  CONSTRAINT `fk_att_section`  FOREIGN KEY (`section_id`)  REFERENCES `sections` (`id`),
  CONSTRAINT `fk_att_shift`    FOREIGN KEY (`shift_id`)    REFERENCES `shifts` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------
-- Raw data from biometric device before processing

CREATE TABLE `biometric_punches` (
  `id`           BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id`   INT UNSIGNED NOT NULL,
  `biometric_id` VARCHAR(30)  NOT NULL,
  `employee_id`  INT UNSIGNED COMMENT 'Filled after matching to employees.biometric_id',
  `punch_time`   DATETIME NOT NULL,
  `punch_type`   ENUM('in', 'out', 'unknown') DEFAULT 'unknown',
  `shift_code`   VARCHAR(10),
  `device_id`    VARCHAR(50),
  `is_processed` TINYINT(1) DEFAULT 0,
  `uploaded_at`  DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_bio_id`    (`biometric_id`),
  KEY `idx_bio_date`  (`punch_time`),
  CONSTRAINT `fk_bio_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- MODULE 8: SHIFT ROSTER
-- ============================================================

CREATE TABLE `shift_rosters` (
  `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id`   INT UNSIGNED NOT NULL,
  `employee_id`  INT UNSIGNED NOT NULL,
  `roster_date`  DATE NOT NULL,
  `shift_id`     INT UNSIGNED NOT NULL,
  `section_id`   INT UNSIGNED,
  `is_week_off`  TINYINT(1) DEFAULT 0,
  `is_holiday`   TINYINT(1) DEFAULT 0,
  `shift_group`  VARCHAR(10),
  `created_by`   INT UNSIGNED,
  `created_at`   DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_roster` (`employee_id`, `roster_date`),
  KEY `idx_roster_date` (`roster_date`),
  CONSTRAINT `fk_roster_company` FOREIGN KEY (`company_id`)  REFERENCES `companies` (`id`),
  CONSTRAINT `fk_roster_emp`     FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`),
  CONSTRAINT `fk_roster_shift`   FOREIGN KEY (`shift_id`)    REFERENCES `shifts` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------
-- Template for rotating shift patterns

CREATE TABLE `shift_roster_groups` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id`  INT UNSIGNED NOT NULL,
  `group_code`  VARCHAR(10)  NOT NULL,
  `name`        VARCHAR(50)  NOT NULL,
  `pattern`     JSON COMMENT '[{"day_offset":0,"shift_code":"A","is_off":false},...]',
  `cycle_days`  INT UNSIGNED DEFAULT 7,
  `is_active`   TINYINT(1) DEFAULT 1,
  `created_at`  DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_rg_code` (`company_id`, `group_code`),
  CONSTRAINT `fk_rg_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- MODULE 9: BILLING
-- ============================================================

CREATE TABLE `billing_periods` (
  `id`               INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id`       INT UNSIGNED NOT NULL,
  `vendor_id`        INT UNSIGNED NOT NULL,
  `period_type`      ENUM('monthly', 'custom') DEFAULT 'monthly',
  `from_date`        DATE NOT NULL,
  `to_date`          DATE NOT NULL,
  `po_number`        VARCHAR(50),
  `wo_number`        VARCHAR(50),
  `status`           ENUM('draft', 'generated', 'submitted', 'approved', 'paid', 'partially_paid') DEFAULT 'draft',
  `total_mandays`    DECIMAL(10,2) DEFAULT 0,
  `gross_amount`     DECIMAL(14,2) DEFAULT 0,
  `deduction_amount` DECIMAL(14,2) DEFAULT 0,
  `net_amount`       DECIMAL(14,2) DEFAULT 0,
  `generated_by`     INT UNSIGNED,
  `generated_at`     DATETIME,
  `created_at`       DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_bp_vendor` (`vendor_id`),
  CONSTRAINT `fk_bp_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`),
  CONSTRAINT `fk_bp_vendor`  FOREIGN KEY (`vendor_id`)  REFERENCES `vendors` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------

CREATE TABLE `billing_line_items` (
  `id`                INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `billing_period_id` INT UNSIGNED NOT NULL,
  `section_id`        INT UNSIGNED NOT NULL,
  `category_id`       INT UNSIGNED NOT NULL,
  `component_id`      INT UNSIGNED NOT NULL,
  `mandays`           DECIMAL(10,2) DEFAULT 0,
  `rate`              DECIMAL(12,2) DEFAULT 0,
  `amount`            DECIMAL(14,2) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `fk_bli_bp` (`billing_period_id`),
  CONSTRAINT `fk_bli_bp` FOREIGN KEY (`billing_period_id`) REFERENCES `billing_periods` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------

CREATE TABLE `payments` (
  `id`                INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id`        INT UNSIGNED NOT NULL,
  `vendor_id`         INT UNSIGNED NOT NULL,
  `billing_period_id` INT UNSIGNED NOT NULL,
  `payment_date`      DATE NOT NULL,
  `amount`            DECIMAL(14,2) NOT NULL,
  `payment_mode`      ENUM('neft', 'rtgs', 'cheque', 'cash', 'other') DEFAULT 'neft',
  `reference_no`      VARCHAR(100),
  `remarks`           TEXT,
  `created_by`        INT UNSIGNED,
  `created_at`        DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_pay_vendor` (`vendor_id`),
  CONSTRAINT `fk_pay_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`),
  CONSTRAINT `fk_pay_vendor`  FOREIGN KEY (`vendor_id`)  REFERENCES `vendors` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- MODULE 10: STATUTORY & NOTIFICATIONS
-- ============================================================

CREATE TABLE `pf_esi_remittances` (
  `id`               INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id`       INT UNSIGNED NOT NULL,
  `vendor_id`        INT UNSIGNED NOT NULL,
  `period_month`     DATE         NOT NULL COMMENT 'First day of the month e.g. 2026-05-01',
  `type`             ENUM('pf', 'esi') NOT NULL,
  `expected_amount`  DECIMAL(12,2) DEFAULT 0,
  `remitted_amount`  DECIMAL(12,2) DEFAULT 0,
  `challan_no`       VARCHAR(100),
  `remittance_date`  DATE,
  `status`           ENUM('pending', 'remitted', 'partially_remitted') DEFAULT 'pending',
  `created_at`       DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_pfesi` (`vendor_id`, `period_month`, `type`),
  KEY `fk_pfesi_vendor` (`vendor_id`),
  CONSTRAINT `fk_pfesi_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`),
  CONSTRAINT `fk_pfesi_vendor`  FOREIGN KEY (`vendor_id`)  REFERENCES `vendors` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------
-- Notification queue — hook for future email/SMS/WhatsApp

CREATE TABLE `notification_queue` (
  `id`                BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id`        INT UNSIGNED NOT NULL,
  `type`              ENUM('email', 'sms', 'whatsapp', 'in_app') DEFAULT 'in_app',
  `recipient_user_id` INT UNSIGNED,
  `recipient_email`   VARCHAR(150),
  `recipient_mobile`  VARCHAR(15),
  `subject`           VARCHAR(255),
  `body`              TEXT,
  `event_type`        VARCHAR(100) COMMENT 'indent_submitted | indent_approved | licence_expiry_alert | etc.',
  `ref_module`        VARCHAR(50),
  `ref_id`            INT UNSIGNED,
  `status`            ENUM('pending', 'sent', 'failed') DEFAULT 'pending',
  `attempts`          TINYINT UNSIGNED DEFAULT 0,
  `sent_at`           DATETIME,
  `created_at`        DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_nq_status` (`status`),
  KEY `idx_nq_type`   (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Add FK for user_sections → sections (defined after sections table)
-- ============================================================

ALTER TABLE `user_sections`
  ADD CONSTRAINT `fk_us_section` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`) ON DELETE CASCADE;

SET FOREIGN_KEY_CHECKS = 1;
