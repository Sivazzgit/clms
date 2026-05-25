# Contract Labour Management System (CLMS)
## Requirements & Design Questionnaire
### For Customer Review & Feedback

---

> **Purpose:** This document is prepared to gather structured requirements from all stakeholders before designing the new CLMS system. Please review each section and provide your answers, corrections, or additions. Your feedback will directly shape the database design and user interface.

---

## SECTION 1 — Organisation & Company Setup

1.1. What is the full legal name of the company (Principal Employer)?
Muthiah Beverage and Confectionery (Pvt) Ltd 

1.2. How many plant locations / factories are to be managed under this system?
- [ ] Single plant
- [] Multiple plants (please list them)

Plants can be created dynamically

1.3. What i s the financial year followed?
- [ ] April – March
- [ ] January – December
- [ ] Other: ___________
April- March
1.4. Are there multiple shifts? If yes, please list all shifts with their timings:

| Shift Code | Shift Name | Start Time | End Time | Remarks |
|---|---|---|---|---|
| A | | | | |
| B | | | | |
| C | | | | |
| General | | | | |
Shifts can be dynamically added by the Employer - HR admin, default Shift 1 - 9-6 - can be modified 
1.5. How many work sections / departments currently use contract labour? (From the existing data, we found: SEP I, SEP II, FGS I, FGS II, EOD, PP2, PP6, Kancolor, Stores, QA Lab, FGS Lab — please confirm or update this list)
these are sample data for a customer. this software can be given to any company with minimal corrections
1.6. Is there a concept of Work Centers (cost centers) mapped to sections? How are billing costs allocated — section-wise or cost-center-wise?
Not known now
1.7. What holidays are observed — Factory Act holidays, national holidays, or company-specific? Is there a holiday calendar to be maintained per year?
yes let the holidays can be imported by HR admin
---

## SECTION 2 — User Roles & Access Control

The following 5 user types are planned. Please confirm or modify:



| # | Role | User ID Pattern | Description | Status |
|---|---|---|---|---|
| 1 | HR Admin | clmsadmin | Full system access — vendor mgmt, approvals, masters | Active |
| 2 | Contractor | clms0xx (e.g., clms01) | Vendor portal — employee list, deployment confirmations | Active |
| 3 | Section In-charge | clms1xxx (e.g., clms1249) | Indent creation, deployment review | Active |
| 4 | HOD | clms2xxx | Indent review and approval | Planned (not activated) |
| 5 | Plant Head / Factory Manager | clms3xxx | Final approval authority | Planned (not activated) |

2.1. Should the HOD and Plant Head roles be activated in the new system?
users can be created by HR for the employer. Mandate the HR user to create 1 user for the role. It can be a single user with multiple roles also. admin can work for all
it can be anyuser. the role is important

2.2. Should each Section In-charge be restricted to only their assigned sections, or can they view all sections?
yes, only that section

2.3. Should a contractor user be able to see only their own employees and their own indent requests?
yes

2.4. Is there a need for a **Security / Gate** user role to record daily attendance entry independently?
- [ ] Yes — Gate staff record daily shift-wise attendance entry
- [ ] No — Attendance is imported from biometric device
both can be possile
2.5. Should the system support **email notifications** at approval stages? If yes, at which steps?
Not now.but in wish list. Put hooks
2.6. What happens if an approver is on leave? Is there a deputy/alternate approver concept?
admin can approve on the behalf.
2.7. Should the system maintain an **audit log** of all changes (who changed what and when)? (Current system partially logs this in free-text fields)
yes. for all
---

## SECTION 3 — Vendor / Contractor Management

3.1. What details are required for registering a contractor agency? Please confirm the fields needed:

| Field | Required? | Notes |
|---|---|---|
| Contractor Code | Yes | Auto-generated or manual? |
| Company Name | Yes | |
| Registered Address | Yes | |
| Contact Person Name | Yes | |
| Mobile Number | Yes | |
| Email ID | Yes | |
| GSTIN (CGST/SGST combined) | Yes | |
| PAN Number | Yes | |
| ESI Employer Code | Yes | |
| PF Employer Code | Yes | |
| Contract Labour Licence Number | Yes | |
| Licence Validity Date | ? | Should expiry alerts be sent? |
| Maximum Employee Limit | Yes | Upper cap on deployable workers |
| Bank Account Details | ? | For payment processing |
| Contract Agreement Start & End Date | ? | |
| Trade / Category of Work | ? | E.g., housekeeping, skilled worker |


3.2. Can a single contractor provide multiple **categories** of workers (e.g., both skilled ITI and unskilled)?
yes possible

3.3. Should contractor registration require **HR approval** before the contractor becomes active?
HR only can add a contractor. The login, later he can use to change the credentials and edit the details.

3.4. Should the system track **contract renewal** dates and send alerts before expiry?
Yes. But not an immediate requirement

3.5. What documents are to be attached per contractor? (e.g., GST certificate, PF registration, labour licence) Should a **document management** module be included?

need to check and get back

---

## SECTION 4 — Labour Category / Job Master

The current system has a `clmsjobmaster` table with wage components. Please clarify:

4.1. What are the **labour categories / grades** used? (e.g., Skilled, Semi-Skilled, Unskilled, ITI, Helper, Supervisor)
Yes. Keep all the possible things. May be HR can add the types as well. No hardcoded types required. May be some defaults which can be edited

4.2. For each category, are the following wage components applicable? (Mark Y/N)

| Component | Applicable | Fixed Amount or % of Basic |
|---|---|---|
| Basic Wage | Y | |
| DA (Dearness Allowance) | ? | |
| HRA (House Rent Allowance) | ? | |
| Uniform Allowance | ? | |
| Washing Allowance | ? | |
| Night Shift Allowance | ? | |
| Safety Shoe Allowance | ? | |
| PF (Employer Contribution) | ? | |
| ESI (Employer Contribution) | ? | |
| Bonus | ? | |
| Leave Encashment | ? | |
| Others | ? | |
| Service Charge (Contractor Margin) | ? | |
| Material Cost | ? | |
| CGST | ? | |
| SGST | ? | |
All Y
4.3. Are wage rates **revision**-enabled (i.e., rates change from a specific date and history is maintained)?
Yes
4.4. Is the billing to the Principal Employer based on **actual attendance** or on a **fixed contract amount** or a **man-day rate**?
based on shift and agreement.

4.5. Are there separate rates for **normal days vs. holiday / overtime**?
yes. need a provision to update this
---

## SECTION 5 — Contract Labour Employee Master

5.1. What details are required when a contractor registers an employee? Confirm the fields:

| Field | Required? |
|---|---|
| Employee ID (auto-generated or manual) | |
| Full Name (First / Last / Middle) | |
| Date of Birth | |
| Gender | |
| Father's Name | |
| Permanent Address | |
| Current Address | |
| Mobile Number | |
| Emergency Contact | |
| Aadhaar Number | |
| PAN Number | |
| ESI Number | |
| PF UAN Number | |
| Bank Account + IFSC | |
| Contractor / Agency Name | |
| Category / Grade (Skilled / ITI etc.) | |
| Date of Joining (with contractor) | |
| Date of Joining (at this plant) | |
| Date of Separation | |
| Photo | |
| Biometric ID (for gate attendance) | |
| Qualification / Trade | |

5.2. Who can **add** a new employee — only the contractor or HR admin too?
both
5.3. Who can **approve** new employee additions — HR admin only or section in-charge as well?
HR admin
5.4. Should there be a **bulk upload** facility (Excel/CSV) for employee master?
Yes
5.5. Can a single employee work for **multiple contractors** at different times? Or is the employee permanently tied to one contractor?
only one as of now.

5.6. Should the system maintain **separation records** (resignation, contract completion, termination)?
yes
5.7. Is biometric integration required? If yes, what brand/model of device is used?
will customise when asked
---

## SECTION 6 — Manpower Indent (Requirement Raising)

The existing `clmsintend` table has a basic indent structure. For the new system:

6.1. **Who initiates the indent?** (Section In-charge / Shop Floor Team)
section in charge
6.2. Should the indent be for a **fixed date range** (e.g., 01-Oct to 31-Jan) or **per month** or **per day**?
all of three above
6.3. Should different indents be raised for **different shifts** (A, B, C, General), or can one indent cover all shifts?
multiple intends for day/shift
6.4. What details are required per indent line?

| Field | Required? |
|---|---|
| Section / Work Center | |
| Shift | |
| Labour Category / Grade | |
| Number of Workers Required | |
| From Date | |
| To Date | |
| Nature of Work / Job Description | |
| Urgency Flag (Normal / Urgent) | |
| Remarks | |
| Preferred Contractor (optional) | |
yes
6.5. **Approval flow for indent** — please confirm the steps:

```
Step 1: Section In-charge creates indent
Step 2: Section HOD reviews and recommends
Step 3: Factory Manager / Plant Head approves
Step 4: HR Manager accepts and assigns to contractor
Step 5: Contractor receives and confirms availability
```
— Is this flow correct? Are any steps to be added, removed, or changed?
yes
6.6. Can an indent be **modified** after it is submitted for approval? If yes, does it restart the approval cycle?
yes restart the approval cycle
6.7. Can an indent be **rejected** or **sent back** with remarks? At which levels?
yes
6.8. Should the system support **recurring indents** (e.g., same requirement every month)?
yes
6.9. Currently, the indent assigns one contractor per section per date range. Should **multiple contractors** be assignable to one indent (split supply)?
yes
6.10. Should there be an **indent vs. actual deployment** comparison report?
yes
---

## SECTION 7 — Manpower Deployment

7.1. Once an indent is approved and accepted by the contractor, how does the contractor confirm **which specific employees** will be deployed?
contractor can decide
7.2. Should the contractor submit a **deployment plan** in advance (e.g., day before shift starts) or is it recorded on the day of attendance?
yes
7.3. The current system records daily manpower in monthly tables (e.g., `clmsmanpower0525`). Should the new system use a **single unified manpower table** with a date column instead of per-month tables?
yes a single unified manpower table
7.4. **Deployment approval flow** — please confirm:

```
Step 1: Contractor submits deployment list
Step 2: Section In-charge / Shop Floor team reviews
Step 3: Section HOD accepts
Step 4: Plant Head gives final approval → triggers daily costing
```
— Is this correct?
yes
7.5. Should the Section In-charge be able to **add/substitute** employees in the deployment list (i.e., if contractor sends Employee A but Section wants Employee B)?
yes
7.6. Should deployment be tracked at the level of **individual employee + date + shift**, or only as **headcount per section + date + shift**?
both way
7.7. How is **absenteeism** handled? If a contractor commits 3 employees for a day but only 2 show up, how should it be recorded and penalized?
yes as per terms decided 
7.8. Should the system generate a **daily consolidated costing report** showing contractor-wise, section-wise manpower cost?
yes
---

## SECTION 8 — Attendance Recording

8.1. How is attendance currently captured?

- [ ] Manual entry at the gate by security / gate staff
- [ ] Biometric punch (in/out) — data imported into system
- [ ] Both
both
8.2. The current system has a `clmspunching` table with IN/OUT records. Should the new system:
- [ ] Continue with manual gate entry
- [ ] Import biometric data (CSV/API)
- [ ] Both options (manual overrides biometric when needed)
both
8.3. Should the system calculate **working hours** from IN/OUT punch time and determine if the shift was completed?
yes
8.4. How should **late arrivals and early departures** be treated? Do they affect payment?
as per policy decided and may or maynot affect payment
8.5. Should the system handle **overtime** recording?
yes
8.6. If an employee has no punch record for a day (they were present per gate entry but biometric failed), what is the exception handling process?
manual entry by contractor with hr approval 
8.7. Are there **weekly off / rest day** rules per shift? (e.g., Sunday off, or rotating off-days)

8. Should **leave records** (Earned Leave, Casual Leave, Sick Leave) be maintained for contract labour as per Factories Act?
not now
---

## SECTION 9 — Statutory Compliance & Reports

9.1. Which statutory forms under the **Contract Labour (Regulation & Abolition) Act** are required?

- [ ] Form XII — Muster Roll
- [ ] Form XIII — Register of Wages
- [ ] Form XIV — Wage Slip
- [ ] Form XVI — Annual Returns
- [ ] Form XIX — Register of Contractors
- [ ] Others (please list): ___________
yes all
9.2. Under the **Factories Act**, are the following registers required?

- [ ] Adult Workers Register
- [ ] Leave with Wages Register
- [ ] Attendance Card
- [ ] Overtime Register
- [ ] Accident Register
- [ ] Others: ___________
yes
9.3. Is **ESI** applicable to all contract workers? What is the current wage ceiling for ESI?
yes
9.4. Is **PF** applicable to all contract workers?
yes
9.5. Should the system calculate and track the **contractor's PF/ESI remittance** and reconcile against expected amounts?
yes
9.6. Should the system generate **Form 12A (PF)** or **ESI returns** format data?
yes
9.7. Are **bonus** and **gratuity** calculations required for contract labour?
yes
9.8. Is there a requirement to track **minimum wage compliance** — i.e., verify contractor's payroll against government-notified minimum wages?
yes
---

## SECTION 10 — Billing & Payments

10.1. How is the contractor's bill calculated? Which of the following methods is used?

- [ ] Man-days × agreed rate per day per category
- [ ] Fixed monthly amount per head
- [ ] Itemised bill (basic + DA + HRA + PF + ESI + service charge + GST)
- [ ] Combination of above
all above
10.2. Should the system generate the **contractor invoice / billing statement**? Or does the contractor raise their own invoice and the system only validates it?
yes
10.3. Is **GST (CGST + SGST / IGST)** applicable on contractor bills?
not now
10.4. Are **deductions** to be applied on contractor bills (e.g., penalty for absenteeism, damage)?
not now
10.5. Is there a **TDS** deduction applicable on contractor payments?
not now
10.6. Should the system support **section-wise / cost-center-wise billing allocation** (i.e., total cost split across departments)?
yes
10.7. Should billing be done **monthly** or can it be done for any date range?
both way
10.8. Is there a **Purchase Order (PO)** or **Work Order (WO)** reference number to be maintained against contractor billing?
yes
10.9. Should the system track **payment made** to contractors and show outstanding amounts?
yes
---

## SECTION 11 — Shift Roster Management

The current system has monthly shift roster tables (e.g., `clmsshiftroster0122`). For the new design:

11.1. Who creates and manages the shift roster for contract labour?
- [ ] HR Admin
- [ ] Section In-charge
- [ ] Contractor (for their own workers)
contractor
11.2. Should the shift roster be created **in advance** (e.g., monthly plan)?
yes monthly plan
11.3. Should the system support **rotating shift groups** (current system has `shiftgroup` concept)?
yes
11.4. What is the maximum number of distinct shift patterns in use (e.g., 3-shift, 2-shift, general)?
at present 4 may increase
11.5. Are there any **flexi-shift** or **12-hour shift** patterns?
yes
11.6. Should the system allow **shift swaps** — where Employee A and Employee B exchange shifts for a day?
yes
---

## SECTION 12 — Integration & Technical Requirements

12.1. Should the new system be a **web application** (accessible from browser)?
yes
12.2. Should it be accessible on **mobile devices** (responsive design)?
yes
12.3. Should there be a dedicated **mobile app** for contractors to submit deployment data?
yes
12.4. What is the expected **number of concurrent users**?
50
12.5. Is the system to be hosted **on-premises** or on **cloud**?
website
12.6. Should the new system integrate with any existing **ERP / HRMS** (SAP, Oracle, Tally etc.)?
yes
12.7. Should the system send **email / SMS / WhatsApp notifications** for:
- Indent approvals
- Deployment confirmations
- Attendance anomalies
- Licence expiry alerts
yes
12.8. Should there be a **data import** facility to migrate existing employee and attendance data from the old system?
yes
12.9. What **database** is preferred for the new system?
- [ ] MySQL / MariaDB (current)
- [ ] PostgreSQL
- [ ] Others
mysql
12.10. What **technology stack** is preferred for the new UI?
- [ ] PHP (current stack)
- [ ] React / Vue.js (modern web)
- [ ] Python / Django
- [ ] No preference
php
---

## SECTION 13 — Reports Required

Please confirm which reports are required and their frequency:

| Report | Required? | Frequency | Notes |
|---|---|---|---|
| Daily Attendance Summary (section-wise, shift-wise) | | Daily | |
| Contractor-wise Headcount Report | | Daily / Monthly | |
| Indent vs. Deployment Variance Report | | Daily | |
| Muster Roll (Form XII) | | Monthly | |
| Wage Register (Form XIII) | | Monthly | |
| PF / ESI Contribution Statement | | Monthly | |
| Contractor-wise Billing Summary | | Monthly | |
| Section-wise / Cost-center-wise Cost Report | | Monthly | |
| Leave Register | | Monthly / Annual | |
| Adult Workers Register | | Annual | |
| Absenteeism Report | | Weekly / Monthly | |
| Gate Attendance Punch Report | | Daily | |
| Employee Separation / Turnover Report | | Monthly | |
| Vendor Registration & Compliance Status | | On-demand | |
all above
---

## SECTION 14 — Current Pain Points & Issues

Please describe the top problems with the current system that the new system must solve:

14.a multiple data storage. want to make single data base_______________________________________________________

14.2. mannual errors_________________________________________________________

14.3. want to make real thime_________________________________________________________

14.4. _want auser friendly__system______________________________________________________

---

## SECTION 15 — Go-Live & Priority

15.1. What is the **expected go-live date** or timeline?
one month or as early as possible
15.2. Which module should go live **first**? (Suggested priority order — please revise as needed)

| Priority | Module |
|---|---|
| 1 | Vendor / Contractor Registration |
| 2 | Contract Labour Employee Master |
| 3 | Shift & Section Master Data |
| 4 | Manpower Indent & Approval |
| 5 | Manpower Deployment |
| 6 | Attendance Recording (Gate Entry) |
| 7 | Biometric Integration |
| 8 | Billing & Payroll |
| 9 | Statutory Reports |
all together
15.3. Is **parallel running** with the old system required during transition?
yes
15.4. Who are the key users to be involved in **User Acceptance Testing (UAT)**?
HR User,Contractor user,section in charge
## NOTES / ADDITIONAL REQUIREMENTS

_Please use this space to capture any requirements not covered above._

___________________________________________
___________________________________________
___________________________________________

---

## WHAT WE FOUND IN THE EXISTING SYSTEM

> This section summarises what is already implemented in the current codebase — for your reference while answering questions above.

### Database Tables Already Existing

| Table | Purpose | Observation |
|---|---|---|
| `clmsvendormaster` | Contractor agency master | Basic details — code, name, GST, PF, ESI, licence, employee limit |
| `clmsemployeemaster` | Contract labour employee master | Comprehensive fields — personal, statutory, bank details |
| `clmsintend` | Manpower indent | Date range, shift-wise headcount (A/B/C shifts), approval tracking in text fields |
| `clmsjobmaster` | Labour category / wage structure | Category-wise wages with all components + billing components |
| `clmssectionmaster` | Section / work center master | Section name, in-charge employee ID, area, cost center |
| `clmsapprovalmatrix` | Approval routing | Section → Creator → Approver → HOD mapping |
| `clmsusersection` | User-to-section mapping | Maps a user ID to one or more sections |
| `clmsmanpower0xxx` | Daily deployment records (per month) | One table per month — date, shift, employee, category, hours |
| `clmsmanpowerapstatus` | Deployment approval status | Date, shift, section, headcount engaged vs. approved |
| `clmspunching` | Biometric/gate punch records | Employee ID, IN/OUT, shift, date, time |
| `clmsshiftroster0xxx` | Monthly shift assignment | Employee, grade, section, shift per day |
| `www_users` | System user login | User ID, password (hashed), access level, blocked flag |

### Modules Already Partially Built

| Module | Files | Status |
|---|---|---|
| Vendor Master View | `vendormaster.php` | View only, no add/edit UI |
| Employee Master | `prlEmployeeMaster.php`, `prlEmployeeMasterAddnew.php`, `prlEmployeeMasterEdit.php` | Functional |
| Manpower Indent | `intend.php`, `intendaddrecord.php`, `intendapprove.php` | Functional, basic UI |
| Gate Manpower Entry | `gatemanpowerentry.php` | Functional — daily shift-wise gate entry |
| Deployment Approval | `manpowerentryapproval.php` | Functional — section in-charge approves |
| Shift Roster | `shiftrosterview.php`, `shiftrosterupdation.php` | Functional |
| Punching / Attendance | `punchingview.php`, `PunchingDataUpload*.php` | Functional — biometric upload |
| Billing Summary | `billingmonthly.php`, `billingcostcenterwisesummary.php` | Functional — read-only reports |
| Statutory Forms | `clmsstatutoryforms.htm` | Static HTML reference only |
| Approval Matrix | `approvalmatrix.php` | Functional |

### Key Design Issues in Current System (Identified)

1. **Separate monthly tables** for manpower and shift roster (e.g., `clmsmanpower0525`, `clmsshiftroster0122`) — not scalable, creates schema sprawl.
2. **Approval status stored as free-text** in `clmsintend.status` column — makes programmatic filtering and status tracking unreliable.
3. **Indent only supports 3 shifts** (A, B, C) as separate integer columns — not extensible to more shifts.
4. **Vendor edit functionality** is marked `notyet.php` — not implemented.
5. **No role-based access control** framework — user roles inferred from user ID prefix pattern only.
6. **Statutory forms** not auto-generated — only a static HTML reference page exists.
7. **Contractor portal** not implemented — contractors cannot log in and confirm deployment.

---

*Document prepared on: 24 May 2026*
*Version: 1.0 — Initial Requirements Gathering*
