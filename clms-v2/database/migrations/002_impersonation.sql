-- =============================================================================
-- CLMS v2 — Migration: Impersonation / Identity Switch
-- Run on target DB once after deploying the feature code.
-- =============================================================================

-- 1. New table: track every impersonation session
CREATE TABLE IF NOT EXISTS `impersonation_log` (
  `id`             INT UNSIGNED     NOT NULL AUTO_INCREMENT,
  `actor_id`       INT UNSIGNED     NOT NULL COMMENT 'Real logged-in user',
  `actor_username` VARCHAR(100)     NOT NULL,
  `target_id`      INT UNSIGNED     NOT NULL COMMENT 'User being impersonated',
  `target_username`VARCHAR(100)     NOT NULL,
  `target_company` INT UNSIGNED     NOT NULL,
  `reason`         VARCHAR(255)     NULL,
  `ip_address`     VARCHAR(45)      NOT NULL,
  `started_at`     DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ended_at`       DATETIME         NULL,
  PRIMARY KEY (`id`),
  KEY `idx_actor`  (`actor_id`),
  KEY `idx_target` (`target_id`),
  KEY `idx_started`(`started_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Add impersonated_by column to existing audit_log
-- (Records which real user performed the action when impersonating)
ALTER TABLE `audit_log`
  ADD COLUMN `impersonated_by` INT UNSIGNED NULL
    COMMENT 'real user id when action done via impersonation'
    AFTER `user_id`;

-- 3. Add super_admin and admin roles
INSERT IGNORE INTO `roles` (`code`, `name`, `description`, `is_active`) VALUES
  ('super_admin', 'Super Admin',    'CLMS platform owner — cross-tenant access, impersonate any user', 1),
  ('admin',       'Company Admin',  'Company-level admin — can impersonate non-admin users within own company', 1);

-- Verification queries:
-- SELECT COUNT(*) FROM impersonation_log;
-- SHOW COLUMNS FROM audit_log LIKE 'impersonated_by';
-- SELECT id, code, name FROM roles WHERE code IN ('super_admin','admin');
