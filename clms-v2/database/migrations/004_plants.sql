-- =============================================================================
-- CLMS v2 — Migration 004: Multi-Plant / Multi-Location Support
-- Adds Plants as first-class entities; Cost Centres, Sections, Shifts, and
-- Holidays become plant-scoped. Introduces the Group Head role.
-- Plant Head responsibility moves from individual Sections → Plant.
-- =============================================================================

SET FOREIGN_KEY_CHECKS = 0;

-- -----------------------------------------------------------------------------
-- 1. Create plants table
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `plants` (
  `id`                  INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id`          INT UNSIGNED NOT NULL,
  `code`                VARCHAR(20)  NOT NULL,
  `name`                VARCHAR(150) NOT NULL,
  `address`             TEXT,
  `city`                VARCHAR(100),
  `state`               VARCHAR(100),
  `country`             VARCHAR(100) DEFAULT 'India',
  `plant_head_user_id`  INT UNSIGNED NULL COMMENT 'Plant Head responsible for this plant',
  `is_active`           TINYINT(1)   DEFAULT 1,
  `created_by`          INT UNSIGNED,
  `created_at`          DATETIME     DEFAULT CURRENT_TIMESTAMP,
  `updated_at`          DATETIME     DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_plant_code` (`company_id`, `code`),
  KEY `fk_plant_company` (`company_id`),
  KEY `fk_plant_head`    (`plant_head_user_id`),
  CONSTRAINT `fk_plant_company` FOREIGN KEY (`company_id`)         REFERENCES `companies` (`id`),
  CONSTRAINT `fk_plant_head`    FOREIGN KEY (`plant_head_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Physical plant locations belonging to a company';

-- -----------------------------------------------------------------------------
-- 2. User ↔ Plant assignment (a user may span multiple plants)
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `user_plants` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`     INT UNSIGNED NOT NULL,
  `plant_id`    INT UNSIGNED NOT NULL,
  `is_primary`  TINYINT(1)   DEFAULT 0 COMMENT '1 = default / home plant',
  `assigned_by` INT UNSIGNED,
  `assigned_at` DATETIME     DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_user_plant` (`user_id`, `plant_id`),
  KEY `fk_up_user`  (`user_id`),
  KEY `fk_up_plant` (`plant_id`),
  CONSTRAINT `fk_up_user`  FOREIGN KEY (`user_id`)  REFERENCES `users`  (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_up_plant` FOREIGN KEY (`plant_id`) REFERENCES `plants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 3. Add plant_id to cost_centers
-- -----------------------------------------------------------------------------
ALTER TABLE `cost_centers`
  ADD COLUMN `plant_id` INT UNSIGNED NULL COMMENT 'Plant this cost centre belongs to'
    AFTER `company_id`,
  ADD KEY `fk_cc_plant` (`plant_id`),
  ADD CONSTRAINT `fk_cc_plant` FOREIGN KEY (`plant_id`) REFERENCES `plants` (`id`) ON DELETE SET NULL;

-- -----------------------------------------------------------------------------
-- 4. Remove plant_head_user_id from sections (moved to plants table)
--    and add plant_id foreign key to sections
-- -----------------------------------------------------------------------------
ALTER TABLE `sections`
  DROP FOREIGN KEY `fk_sec_plant_head`,
  DROP KEY         `fk_sec_plant_head`,
  DROP COLUMN      `plant_head_user_id`;

ALTER TABLE `sections`
  ADD COLUMN `plant_id` INT UNSIGNED NULL COMMENT 'Plant this section belongs to'
    AFTER `company_id`,
  ADD KEY `fk_sec_plant` (`plant_id`),
  ADD CONSTRAINT `fk_sec_plant` FOREIGN KEY (`plant_id`) REFERENCES `plants` (`id`) ON DELETE SET NULL;

-- -----------------------------------------------------------------------------
-- 5. Add plant_id to shifts
-- -----------------------------------------------------------------------------
ALTER TABLE `shifts`
  ADD COLUMN `plant_id` INT UNSIGNED NULL COMMENT 'Plant this shift applies to (NULL = all plants)'
    AFTER `company_id`,
  ADD KEY `fk_shift_plant` (`plant_id`),
  ADD CONSTRAINT `fk_shift_plant` FOREIGN KEY (`plant_id`) REFERENCES `plants` (`id`) ON DELETE SET NULL;

-- -----------------------------------------------------------------------------
-- 6. Add plant_id to holidays
-- -----------------------------------------------------------------------------
ALTER TABLE `holidays`
  ADD COLUMN `plant_id` INT UNSIGNED NULL COMMENT 'Plant this holiday applies to (NULL = all plants)'
    AFTER `company_id`,
  ADD KEY `fk_holiday_plant` (`plant_id`),
  ADD CONSTRAINT `fk_holiday_plant` FOREIGN KEY (`plant_id`) REFERENCES `plants` (`id`) ON DELETE SET NULL;

-- -----------------------------------------------------------------------------
-- 7. Add Group Head role
-- -----------------------------------------------------------------------------
INSERT IGNORE INTO `roles` (`code`, `name`, `description`, `is_active`)
VALUES ('group_head', 'Group Head', 'Head of all plants — company-wide oversight and final approval authority above Plant Head', 1);

SET FOREIGN_KEY_CHECKS = 1;
