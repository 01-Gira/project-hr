# Absensi — Development Roadmap

## Roadmap Policy

Every phase must be implemented incrementally.

A phase is considered complete only if:

- migrations are complete;
- models/relationships are complete;
- authorization is complete;
- validation is complete;
- UI is usable;
- automated tests exist;
- tests pass;
- documentation/status file is updated.

Do not begin advanced payroll logic while attendance core is unstable.

---

# PHASE 0 — Project Foundation

## Goal

Create maintainable Laravel foundation.

## Tasks

- [ ] Initialize Laravel project.
- [ ] Configure PostgreSQL.
- [ ] Configure Redis.
- [ ] Configure queue.
- [ ] Configure application timezone strategy.
- [ ] Authentication.
- [ ] Base dashboard.
- [ ] Install/configure role-permission package.
- [ ] Permission seeder.
- [ ] Default roles seeder.
- [ ] Audit logging foundation.
- [ ] Common enums / statuses.
- [ ] Exception handling conventions.
- [ ] Test infrastructure.
- [ ] CI commands/documentation.

## Exit criteria

- login works;
- protected dashboard works;
- super-admin seeded;
- permission checks work;
- audit can record a sample admin action;
- tests green.

---

# PHASE 1 — Organization & Employee Master

## Goal

Build workforce master data.

## Modules

- Company.
- Branch.
- Department.
- Position.
- Employee.
- Employee account linking.
- Employee manager.
- Employee photo.
- Employee status.

## Tasks

- [ ] Company CRUD.
- [ ] Branch CRUD.
- [ ] Department CRUD.
- [ ] Position CRUD.
- [ ] Employee CRUD.
- [ ] Employee account creation/linking.
- [ ] Employee manager assignment.
- [ ] Employee filters/search.
- [ ] Employee active/inactive flow.
- [ ] Permissions.
- [ ] Tests.

## Exit criteria

HR can create a complete employee record and assign company structure.

---

# PHASE 2 — Employee ID Card

## Goal

Generate printable employee cards.

## Tasks

- [ ] ID card template model.
- [ ] Default CR80 template.
- [ ] Employee ID card issuance.
- [ ] Card number generation.
- [ ] Public verification token.
- [ ] Front/back preview.
- [ ] PDF rendering.
- [ ] Print view.
- [ ] Mark card lost.
- [ ] Revoke card.
- [ ] Replace card.
- [ ] Expiration support.
- [ ] Public verification endpoint.
- [ ] Audit logging.
- [ ] Tests.

## Security

Permanent ID-card QR must never act as reusable attendance QR.

---

# PHASE 3 — Shift & Scheduling

## Goal

Determine expected employee work times.

## Tasks

- [ ] Shift CRUD.
- [ ] Cross-day shift support.
- [ ] Work modes.
- [ ] Employee schedule CRUD.
- [ ] Schedule calendar.
- [ ] Holiday CRUD.
- [ ] Schedule validation.
- [ ] Manager/team schedule view.
- [ ] Tests.

---

# PHASE 4 — Attendance Core & QR

## Goal

Produce reliable attendance records.

## Modules

- Attendance policy.
- QR session.
- Employee QR generation.
- Scanner.
- Attendance event.
- Attendance calculation.
- Daily attendance.

## Tasks

- [ ] Attendance policy CRUD.
- [ ] Secure QR session service.
- [ ] Token hashing.
- [ ] QR TTL.
- [ ] Single-use QR.
- [ ] QR revocation.
- [ ] QR generation rate limiting.
- [ ] Authorized scanner page.
- [ ] Check-in action.
- [ ] Check-out action.
- [ ] Attendance event append-only persistence.
- [ ] Daily attendance calculator.
- [ ] Late calculation.
- [ ] Early leave calculation.
- [ ] Missing checkout status.
- [ ] Race-condition protection.
- [ ] Duplicate scan protection.
- [ ] Employee attendance history.
- [ ] HR attendance view.
- [ ] Tests for concurrent/double scan behavior.

## Exit criteria

Employee can generate a short-lived QR, authorized operator can scan it, and exactly one valid attendance event is produced.

---

# PHASE 5 — Geolocation & Geofence

## Goal

Restrict attendance to valid locations when policy requires it.

## Tasks

- [ ] Location CRUD.
- [ ] Geofence CRUD.
- [ ] Map coordinate input.
- [ ] Employee-to-geofence assignment.
- [ ] Browser geolocation.
- [ ] Distance calculation.
- [ ] Geofence radius validation.
- [ ] GPS accuracy validation.
- [ ] Persist geolocation evidence.
- [ ] Policy integration.
- [ ] WFH exceptions.
- [ ] Field work mode.
- [ ] Failure UX.
- [ ] Tests.

## Exit criteria

Attendance can be rejected with clear evidence when user is outside permitted geofence.

---

# PHASE 6 — Leave, Permission & Attendance Corrections

## Goal

Handle real-world attendance exceptions.

## Tasks

- [ ] Leave type CRUD.
- [ ] Leave request.
- [ ] Sick request.
- [ ] Permission request.
- [ ] Approval workflow.
- [ ] Manager/team approval scope.
- [ ] Attachment support.
- [ ] Attendance correction request.
- [ ] Approval/rejection.
- [ ] Correction application service.
- [ ] Immutable old attendance evidence.
- [ ] Audit log.
- [ ] Tests.

---

# PHASE 7 — Salary Management

## Goal

Manage employee compensation with history.

## Tasks

- [ ] Salary component CRUD.
- [ ] Employee salary CRUD.
- [ ] Effective date support.
- [ ] Prevent overlapping salary periods.
- [ ] Employee salary component CRUD.
- [ ] Salary history UI.
- [ ] Separate salary permissions.
- [ ] Salary audit logs.
- [ ] Tests.

## Exit criteria

Updating an employee salary never modifies historical salary records.

---

# PHASE 8 — Reports

## Goal

Provide HR/owner operational reporting.

## Reports

- Daily attendance.
- Monthly attendance.
- Late employees.
- Absence.
- Leave.
- Missing checkout.
- Work duration.
- Employee attendance history.
- Department/branch attendance summary.

## Tasks

- [ ] Report filters.
- [ ] CSV export.
- [ ] Excel export.
- [ ] PDF export where useful.
- [ ] Permission scopes.
- [ ] Pagination for large datasets.
- [ ] Optimized queries/index verification.
- [ ] Tests.

---

# PHASE 9 — Telegram Control Room

## Goal

Provide configurable admin/owner notification and reporting interface.

## Tasks

- [ ] Telegram bot configuration.
- [ ] Secure Telegram account pairing.
- [ ] Authorized user mapping.
- [ ] Permission-aware commands.
- [ ] `/status`.
- [ ] `/today`.
- [ ] `/late`.
- [ ] `/absent`.
- [ ] `/employee`.
- [ ] `/report`.
- [ ] Notification subscription CRUD.
- [ ] Immediate notification mode.
- [ ] Digest mode.
- [ ] Branch/department scopes.
- [ ] Queue-based delivery.
- [ ] Delivery log.
- [ ] Daily summary scheduler.
- [ ] Error/retry handling.
- [ ] Audit.
- [ ] Tests.

## Security

Telegram access is inherited from application user permissions.

Never grant access solely because a Telegram user knows the bot username.

---

# PHASE 10 — Payroll

## Goal

Calculate payroll from salary + attendance data.

## Tasks

- [ ] Payroll periods.
- [ ] Salary snapshot by effective date.
- [ ] Earnings.
- [ ] Deductions.
- [ ] Attendance deduction policy.
- [ ] Overtime integration.
- [ ] Payroll calculation.
- [ ] Payroll review.
- [ ] Approval.
- [ ] Lock period.
- [ ] Payslip.
- [ ] Export.
- [ ] Audit.
- [ ] Tests.

Do not automatically infer payroll legal/tax rules. Make statutory calculations configurable and separately validated.

---

# PHASE 11 — Advanced Attendance Security

## Tasks

- [ ] Registered device policy.
- [ ] Device revocation.
- [ ] Selfie attendance.
- [ ] Office network/IP policy.
- [ ] Suspicious attendance detection.
- [ ] QR abuse monitoring.
- [ ] Operator kiosk mode.
- [ ] Kiosk device registration.

---

# PHASE 12 — PWA / API / Mobile

## Goal

Expose attendance domain to mobile clients without rewriting business logic.

## Tasks

- [ ] Versioned API.
- [ ] Sanctum/token auth.
- [ ] Mobile attendance endpoints.
- [ ] Push notifications.
- [ ] PWA manifest/offline shell.
- [ ] Flutter-ready API documentation.

---

# Recommended Implementation Order

```text
0 Foundation
   ↓
1 Organization
   ↓
2 ID Card
   ↓
3 Schedule
   ↓
4 Attendance + QR
   ↓
5 Geofence
   ↓
6 Leave/Correction
   ↓
7 Salary
   ↓
8 Reports
   ↓
9 Telegram
   ↓
10 Payroll
   ↓
11 Security+
   ↓
12 Mobile/PWA
```

---

# Definition of Done for Every Module

- [ ] Migration.
- [ ] Model.
- [ ] Factory.
- [ ] Seeder if relevant.
- [ ] Request validation.
- [ ] Policy/permissions.
- [ ] Domain Action/Service.
- [ ] Controller/Livewire layer.
- [ ] UI.
- [ ] Audit events where sensitive.
- [ ] Unit tests.
- [ ] Feature tests.
- [ ] No failing tests.
- [ ] Formatting/linting passes.
- [ ] Implementation status updated.
