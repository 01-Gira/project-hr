# Absensi — Codex Task Queue

This is the implementation queue for Codex.

## How Codex Should Work

For each requested task:

1. read:
   - `00_PROJECT_CONTEXT.md`
   - `01_ERD.md`
   - `02_DATABASE_DESIGN.md`
   - `03_ROADMAP.md`
   - `04_IMPLEMENTATION_STATUS.md`
   - `05_IMPLEMENTATION_RULES.md`
2. inspect actual repository;
3. reconcile blueprint against existing code;
4. implement only the requested phase/task;
5. run tests;
6. update status documentation.

---

# TASK A0 — Repository Audit

## Objective

Determine current project state before writing code.

## Required output

Create/update:

```text
docs/implementation/REPOSITORY_AUDIT.md
```

Include:

- Laravel version.
- PHP version requirement.
- Frontend stack.
- Database driver.
- Redis status/config.
- Authentication stack.
- Authorization stack.
- Existing migrations.
- Existing models.
- Existing modules.
- Existing test count.
- Existing lint/format tools.
- Relevant technical debt.
- Conflicts between blueprint and actual code.

Do not implement product features during audit unless necessary to fix a blocking bootstrap issue explicitly requested by user.

---

# TASK A1 — Phase 0 Foundation

After audit, implement Phase 0.

Expected result:

- working auth;
- RBAC;
- default roles/permissions;
- super admin bootstrap;
- base dashboard;
- audit foundation;
- tests.

---

# TASK A2 — Organization & Employee

Implement Phase 1 after Phase 0 passes.

Priority:

```text
Company
Branch
Department
Position
Employee
```

Employee list must support:

- search;
- status filter;
- branch filter;
- department filter;
- pagination.

---

# TASK A3 — ID Card

Implement printable CR80 card.

Required:

- card issue;
- card number;
- QR public verification;
- preview;
- front/back;
- print/PDF;
- revoke/lost/replace;
- permissions;
- audit.

Do not reuse ID-card QR as attendance token.

---

# TASK A4 — Schedule

Implement:

- shifts;
- cross-day shifts;
- employee schedules;
- holidays;
- work mode.

---

# TASK A5 — Attendance Core

This is the highest-risk module.

Codex must prioritize correctness over UI polish.

Required flow:

```text
Generate QR
    ↓
Secure short-lived session
    ↓
Authorized scanner
    ↓
Policy validation
    ↓
Schedule resolution
    ↓
Atomic QR consume
    ↓
Attendance event
    ↓
Attendance recalculation
```

Required tests include duplicate/concurrent scan.

---

# TASK A6 — Geofence

Implement after attendance core.

Required evidence per attendance event:

```text
latitude
longitude
accuracy
distance
matched geofence
radius
validation result
```

---

# TASK A7 — Exceptions

Implement:

- leave;
- sick;
- permission;
- correction;
- approval.

---

# TASK A8 — Salary

Implement effective-dated salary history.

Do not start payroll yet.

---

# TASK A9 — Reports

Implement operational reports with filters/export.

---

# TASK A10 — Telegram

Implement permission-aware Telegram control room.

Priority commands:

```text
/status
/today
/late
/absent
/employee
/report
```

Add configurable notification subscription.

Do not send every check-in to owner by default.

---

# TASK A11 — Payroll

Implement only after attendance + salary + reports are stable.

---

# Suggested First Prompt to Codex

Use this after adding these documentation files to the repository:

```text
Read all Absensi blueprint files in docs/absensi/ in numeric order.

Start with TASK A0 — Repository Audit only.

Inspect the actual repository before making assumptions. Compare the current Laravel project against the ERD, database design, roadmap, implementation status, and implementation rules.

Create docs/implementation/REPOSITORY_AUDIT.md containing:
- actual Laravel/PHP/frontend/database/auth/RBAC stack,
- existing migrations/models/modules,
- current tests and tooling,
- what is already implemented,
- what is missing,
- conflicts or changes needed versus the blueprint,
- recommended exact scope for Phase 0.

Do not implement Phase 0 yet unless a minimal change is strictly required to make the repository inspectable.

Update 04_IMPLEMENTATION_STATUS.md based only on verified repository evidence.

Do not mark anything DONE without code/tests proving it.
```
