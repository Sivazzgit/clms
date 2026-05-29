-- =============================================================================
-- CLMS v2 — Migration 003: Section Personnel Assignments
-- Adds HOD and Plant Head user links to each section.
-- Section In-charge assignments continue to use the user_sections table.
-- =============================================================================

-- 1. Add HOD and Plant Head columns to sections
ALTER TABLE `sections`
  ADD COLUMN `hod_user_id`        INT UNSIGNED NULL
    COMMENT 'Head of Department overseeing this section'
    AFTER `cost_center_id`,
  ADD COLUMN `plant_head_user_id` INT UNSIGNED NULL
    COMMENT 'Plant Head responsible for approvals for this section'
    AFTER `hod_user_id`,
  ADD KEY `fk_sec_hod`        (`hod_user_id`),
  ADD KEY `fk_sec_plant_head` (`plant_head_user_id`),
  ADD CONSTRAINT `fk_sec_hod`        FOREIGN KEY (`hod_user_id`)        REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_sec_plant_head` FOREIGN KEY (`plant_head_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;
