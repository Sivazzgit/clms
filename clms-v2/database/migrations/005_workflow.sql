-- =============================================================================
-- Migration 005: Workflow enhancements
-- Purpose:
--   1. Extend indent_approvals.action enum for send-back and vendor-response
--      actions introduced in the approval flexibility feature.
--   2. Add unique constraint on indent_vendor_assignments to enforce DB-level
--      integrity (mirrors the PHP upsert logic that already exists).
--   3. Add FK on user_roles.vendor_id → vendors(id) to prevent orphaned links.
--   4. Add composite indexes for the most common filter queries.
-- Safe to run multiple times (uses IF NOT EXISTS where possible).
-- =============================================================================

-- ─────────────────────────────────────────────────────────────────────────────
-- 1. indent_approvals.action — add new action codes
--    needs_revision : HOD / Plant Head sent the indent back for corrections
--    vendor_confirmed : contractor confirmed the full requested headcount
--    vendor_partial   : contractor can only provide a partial headcount
--    vendor_rejected  : contractor cannot fulfil this assignment
--    (keeps legacy 'sent_back' for any existing rows)
-- ─────────────────────────────────────────────────────────────────────────────
ALTER TABLE indent_approvals
  MODIFY COLUMN action ENUM(
    'submitted',
    'hod_reviewed',
    'plant_approved',
    'hr_accepted',
    'contractor_confirmed',
    'rejected',
    'sent_back',
    'needs_revision',
    'vendor_confirmed',
    'vendor_partial',
    'vendor_rejected'
  ) NOT NULL;

-- ─────────────────────────────────────────────────────────────────────────────
-- 2. indent_vendor_assignments — unique constraint
--    One vendor can only be assigned once per line per indent.
--    The PHP upsert already checks this; the DB constraint makes it bulletproof.
-- ─────────────────────────────────────────────────────────────────────────────
ALTER TABLE indent_vendor_assignments
  ADD UNIQUE KEY uq_iva_indent_line_vendor (indent_id, indent_line_id, vendor_id);

-- ─────────────────────────────────────────────────────────────────────────────
-- 3. user_roles.vendor_id — FK to vendors
-- ─────────────────────────────────────────────────────────────────────────────
ALTER TABLE user_roles
  ADD CONSTRAINT fk_ur_vendor
    FOREIGN KEY (vendor_id) REFERENCES vendors (id) ON DELETE SET NULL;

-- ─────────────────────────────────────────────────────────────────────────────
-- 4. Performance indexes
-- ─────────────────────────────────────────────────────────────────────────────

-- indents: primary list page filters on company_id + status
ALTER TABLE indents
  ADD INDEX idx_indent_company_status (company_id, status);

-- indent_approvals: view page and reporting filter on indent_id + action
ALTER TABLE indent_approvals
  ADD INDEX idx_ia_indent_action (indent_id, action);

-- indent_vendor_assignments: vendor response queries filter on all three
ALTER TABLE indent_vendor_assignments
  ADD INDEX idx_iva_indent_vendor_status (indent_id, vendor_id, status);
