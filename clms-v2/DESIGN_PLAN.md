# CLMS 2.0 — System Design & Implementation Plan

---

## 1. TECHNOLOGY STACK

| Layer | Choice | Reason |
|---|---|---|
| Backend | PHP 8.x | Existing team familiarity |
| Database | MySQL 8.x | Existing infrastructure |
| Frontend | HTML5 + Vanilla JS (ES6+) | No build tools, easy to maintain |
| CSS | Custom Design System (CSS Variables) | Easy theme changes, no framework dependency |
| Web Server | Apache + mod_rewrite | Existing Docker setup |

---

## 2. FOLDER STRUCTURE

```
clms-v2/
├── index.php                   ← Entry point / router
├── .htaccess                   ← URL rewriting
├── config/
│   ├── app.php                 ← Constants, DB config, session settings
│   └── roles.php               ← Role definitions, menus per role
├── core/
│   ├── Auth.php                ← Login, session, role checks
│   ├── DB.php                  ← PDO database wrapper (singleton)
│   ├── AuditLogger.php         ← Write to audit_log table
│   ├── Helpers.php             ← Utility functions
│   └── Router.php              ← Simple URL → module mapper
├── modules/
│   ├── auth/
│   │   ├── login.php
│   │   └── logout.php
│   ├── dashboard/
│   │   └── index.php           ← Role-aware dashboard
│   ├── users/
│   │   ├── index.php           ← List users
│   │   ├── form.php            ← Add / Edit
│   │   └── actions.php         ← Backend: save, delete, block
│   ├── vendors/
│   │   ├── index.php
│   │   ├── form.php
│   │   ├── view.php
│   │   └── actions.php
│   ├── employees/
│   │   ├── index.php
│   │   ├── form.php
│   │   ├── view.php
│   │   └── actions.php
│   ├── masters/
│   │   ├── shifts.php
│   │   ├── sections.php
│   │   ├── categories.php
│   │   ├── holidays.php
│   │   └── wage_rates.php
│   ├── indent/
│   │   ├── index.php
│   │   ├── form.php
│   │   ├── view.php
│   │   ├── approve.php
│   │   └── actions.php
│   ├── deployment/
│   │   ├── index.php
│   │   ├── form.php
│   │   ├── approve.php
│   │   └── actions.php
│   ├── attendance/
│   │   ├── gate_entry.php
│   │   ├── biometric.php
│   │   ├── view.php
│   │   └── actions.php
│   ├── reports/
│   │   └── index.php
│   └── billing/
│       ├── index.php
│       ├── generate.php
│       └── view.php
├── templates/
│   ├── base.html.php           ← Master layout (wraps every page)
│   ├── header.html.php         ← Top navigation bar
│   ├── sidebar.html.php        ← Role-based left sidebar
│   └── footer.html.php         ← Closing scripts
├── assets/
│   ├── css/
│   │   ├── theme.css           ← CSS variables, colors, typography
│   │   ├── layout.css          ← Page structure: sidebar + content area
│   │   ├── components.css      ← Buttons, forms, tables, cards, badges
│   │   └── responsive.css      ← Mobile breakpoints
│   ├── js/
│   │   ├── app.js              ← App bootstrap, CSRF, global handlers
│   │   ├── api.js              ← Fetch wrapper for AJAX calls
│   │   ├── forms.js            ← Client-side validation, dynamic fields
│   │   ├── tables.js           ← Sort, filter, export to Excel
│   │   └── utils.js            ← Date helpers, toast, modal, number format
│   └── images/
│       └── logo.svg
├── database/
│   ├── 01_schema.sql           ← Full DB creation script
│   ├── 02_roles_seed.sql       ← Default roles + permissions
│   └── 03_defaults_seed.sql    ← Default wage components, categories
└── uploads/
    ├── vendor_docs/
    ├── employee_photos/
    └── biometric_imports/
```

---

## 3. UX PHILOSOPHY

### Principles
1. **Clarity over density** — Only show what the user needs for their role
2. **Action-oriented** — Every screen has a clear primary action
3. **Status visibility** — Approval states always visible via colour-coded badges
4. **Undo-safe** — Destructive actions (Delete) require confirmation and are red
5. **Mobile-first** — Sidebar collapses to hamburger on mobile
6. **Progressive disclosure** — Complex forms shown step-by-step where possible

### Layout Pattern — Every Page
```
┌────────────────────────────────────────────────────┐
│ HEADER: Logo | Page Title | User Name | Logout     │
├──────────────┬─────────────────────────────────────┤
│              │ BREADCRUMB                          │
│  SIDEBAR     ├─────────────────────────────────────┤
│  (role-based │                                     │
│   nav menu)  │  MAIN CONTENT AREA                  │
│              │  (cards, tables, forms)             │
│              │                                     │
│              │                                     │
├──────────────┴─────────────────────────────────────┤
│ FOOTER: © Company | Version                        │
└────────────────────────────────────────────────────┘
```

### Colour System
| Token | Hex | Usage |
|---|---|---|
| `--clr-primary` | `#1565C0` | Sidebar bg, primary buttons, links |
| `--clr-primary-hover` | `#0D47A1` | Button hover |
| `--clr-primary-light` | `#1976D2` | Active nav item, highlights |
| `--clr-primary-pale` | `#E3F2FD` | Table row hover, badge bg |
| `--clr-accent` | `#2196F3` | Info badges, secondary buttons |
| `--clr-bg` | `#F4F6F9` | Page background |
| `--clr-surface` | `#FFFFFF` | Cards, modals, forms |
| `--clr-border` | `#DDE3ED` | All borders |
| `--clr-text` | `#1C2B45` | Primary text |
| `--clr-text-muted` | `#607080` | Labels, secondary text |
| `--clr-danger` | `#C62828` | Delete buttons, error alerts |
| `--clr-danger-light` | `#FFEBEE` | Error badge backgrounds |
| `--clr-warning` | `#E65100` | Warning badges, urgent flags |
| `--clr-success` | `#2E7D32` | Approved badges, success alerts |
| `--clr-sidebar-text` | `#FFFFFF` | Sidebar text |

### Status Badge Colours
| Status | Colour |
|---|---|
| Draft | Grey |
| Submitted / Pending | Blue |
| HOD Reviewed | Teal |
| Approved | Green |
| Rejected | Red |
| Urgent | Orange |
| Active | Green |
| Suspended | Red |
| Pending Approval | Amber |

---

## 4. USER ROLES & MENUS

### Role Definitions

| Code | Name | Description |
|---|---|---|
| `hr_admin` | HR Admin | Full system access |
| `contractor` | Contractor | Own vendor portal |
| `section_incharge` | Section In-charge | Own sections only |
| `hod` | HOD | Review + approve indents |
| `plant_head` | Plant Head / Factory Manager | Final approval |
| `gate_staff` | Gate / Security Staff | Daily attendance entry |

> A single user can hold multiple roles. Admin can act on behalf of any approver.

---

### Menus Per Role

#### HR Admin
- Dashboard
- Masters
  - Company Setup
  - Shifts
  - Sections & Cost Centers
  - Labour Categories
  - Wage Components & Rates
  - Holiday Calendar
- User Management
  - Users List
  - Roles & Section Assignment
- Vendor Management
  - Contractor List
  - Add / Edit Contractor
  - Documents
- Employee Master
  - All Employees
  - Pending Approvals
  - Separations
  - Bulk Upload
- Indent Management
  - All Indents
  - Vendor Assignment
- Deployment
  - Daily Deployment View
  - Approval Queue
- Attendance
  - Gate Entry
  - Biometric Upload
  - Attendance View
- Billing
  - Generate Bill
  - Bill List
  - Payments
- Statutory Reports
  - Muster Roll (Form XII)
  - Wage Register (Form XIII)
  - PF / ESI Statement
  - Form 12A
  - Adult Workers Register
- Reports
  - Daily Manpower Summary
  - Indent vs. Deployment
  - Absenteeism Report
  - Cost Center Report

#### Contractor
- Dashboard (own summary)
- My Employees
  - Employee List
  - Add / Edit Employee
- Indent Requests (assigned to me)
- Deployment
  - Submit Deployment Plan
  - Deployment History
- Attendance (own workers)
- My Bills

#### Section In-charge
- Dashboard (my sections)
- Indent
  - My Indents
  - Create New Indent
- Deployment
  - Review Deployment
  - Gate Entry (if also gate staff)
- Attendance View (my section)
- Reports (my section)

#### HOD
- Dashboard
- Indent Approval Queue
- Deployment Acceptance Queue
- Reports (under HOD)

#### Plant Head
- Dashboard
- Final Approval Queue (Indents)
- Final Approval Queue (Deployment)
- Daily Cost Report

#### Gate Staff
- Dashboard
- Daily Gate Entry
- View Attendance

---

## 5. MODULE-BY-MODULE DB SCHEMA OVERVIEW

### Module 1 — Foundation
Tables: `companies`, `users`, `roles`, `user_roles`, `user_sections`, `audit_log`, `user_sessions`

### Module 2 — Master Data
Tables: `cost_centers`, `sections`, `shifts`, `holidays`, `labour_categories`, `wage_components`, `category_wage_rates`

### Module 3 — Vendor Management
Tables: `vendors`, `vendor_documents`

### Module 4 — Employee Master
Tables: `employees`, `employee_separations`

### Module 5 — Manpower Indent
Tables: `indents`, `indent_lines`, `indent_approvals`, `indent_vendor_assignments`

### Module 6 — Deployment
Tables: `deployment_plans`, `deployment_plan_employees`, `deployment_approvals`

### Module 7 — Attendance
Tables: `attendance`, `biometric_punches`

### Module 8 — Shift Roster
Tables: `shift_rosters`, `shift_roster_groups`

### Module 9 — Billing
Tables: `billing_periods`, `billing_line_items`, `payments`

### Module 10 — Statutory
Tables: `pf_esi_remittances`, `notification_queue`

---

## 6. APPROVAL WORKFLOW — INDENT

```
[Section IC] → Draft → Submit
                ↓
[HOD]        → Review → Recommend / Send Back
                ↓
[Plant Head] → Approve / Reject
                ↓
[HR Admin]   → Accept → Assign Vendor(s)
                ↓
[Contractor] → Confirm Availability
                ↓
             INDENT LIVE
```

Each step is recorded in `indent_approvals` table with timestamp and remarks.

---

## 7. APPROVAL WORKFLOW — DEPLOYMENT

```
[Contractor] → Submit Deployment Plan (employee list + date + shift)
                ↓
[Section IC] → Review → Modify / Accept
                ↓
[HOD]        → Accept Deployment Manning
                ↓
[Plant Head] → Final Approve → Triggers Daily Cost Calculation
```

---

## 8. IMPLEMENTATION PHASES

### Phase 1 — Foundation (Weeks 1–2)
- [ ] DB schema creation
- [ ] Core PHP framework (Auth, DB, Router)
- [ ] Design system (CSS + JS)
- [ ] Login page
- [ ] Role-based dashboard
- [ ] User management (CRUD + role assignment)

### Phase 2 — Masters (Week 3)
- [ ] Company setup
- [ ] Shift master
- [ ] Section & cost center master
- [ ] Labour categories
- [ ] Wage components & rates
- [ ] Holiday calendar

### Phase 3 — Vendor & Employee (Weeks 4–5)
- [ ] Vendor registration (HR creates, vendor edits own profile)
- [ ] Vendor document upload
- [ ] Employee master (add, approve, edit, separate)
- [ ] Employee bulk upload (CSV template)

### Phase 4 — Indent Workflow (Weeks 6–7)
- [ ] Indent creation (Section IC)
- [ ] HOD review
- [ ] Plant Head approval
- [ ] HR acceptance + vendor assignment
- [ ] Contractor confirmation

### Phase 5 — Deployment Workflow (Weeks 8–9)
- [ ] Contractor submits deployment plan
- [ ] Section IC review
- [ ] HOD acceptance
- [ ] Plant Head final approval
- [ ] Daily costing calculation

### Phase 6 — Attendance (Weeks 10–11)
- [ ] Gate manual entry (Security/Gate staff)
- [ ] Biometric CSV upload + matching
- [ ] Punch exception handling
- [ ] Worked hours calculation
- [ ] Overtime recording

### Phase 7 — Billing (Weeks 12–13)
- [ ] Bill generation (man-day × rate)
- [ ] Itemised bill view
- [ ] Section-wise cost allocation
- [ ] Payment recording
- [ ] Outstanding balance report

### Phase 8 — Statutory Reports (Weeks 14–15)
- [ ] Muster Roll (Form XII)
- [ ] Wage Register (Form XIII)
- [ ] PF/ESI statement
- [ ] Adult Workers Register
- [ ] Leave Register

### Phase 9 — Polish & Hardening (Week 16)
- [ ] Mobile responsive testing
- [ ] Audit log UI
- [ ] Data migration tools (old → new DB)
- [ ] Performance optimisation (indexes, pagination)
- [ ] UAT with client

---

## 9. SECURITY CONTROLS

- Passwords: `password_hash()` with `PASSWORD_BCRYPT`
- Sessions: Secure, HTTPOnly cookies; session regeneration on login
- CSRF: Token per form, validated server-side
- SQL: PDO prepared statements throughout — no string concatenation queries
- File uploads: MIME-type check, extension whitelist, stored outside webroot or behind access control
- Audit: Every INSERT / UPDATE / DELETE logged with user + timestamp
- Role checks: Every module page checks `Auth::requireRole(['hr_admin'])` before rendering

---

*Document version: 1.0 | Date: 25 May 2026*
