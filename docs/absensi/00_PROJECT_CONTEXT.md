# Absensi — Project Context

## 1. Project Identity

**Project name:** Absensi  
**Primary framework:** Laravel  
**Architecture:** Modular monolith  
**Primary database:** PostgreSQL  
**Frontend recommendation:** Blade + Livewire + Alpine.js + Tailwind CSS  
**Cache / Queue:** Redis  
**Authorization:** Role & Permission based access control, recommended with `spatie/laravel-permission`

The application is an attendance and employee management system that can later grow into an HR/payroll platform.

The first implementation must prioritize a clean domain model, auditability, security, and extensibility. Avoid coupling attendance directly to QR codes because QR is only one method for producing an attendance event.

---

## 2. Core Product Goals

The system must support:

- User authentication.
- Master users.
- Employee profiles.
- Roles.
- Permissions.
- Company / branch structure.
- Departments.
- Positions.
- Shifts.
- Employee schedules.
- Holidays.
- Attendance through temporary QR.
- QR expiration.
- QR anti-replay / one-time usage.
- Employee-generated QR.
- Authorized scanner / kiosk.
- Geolocation.
- Geofence.
- Attendance history.
- Attendance correction workflow.
- Leave / permission / sick workflows.
- ID card generation and printing.
- ID card QR verification.
- Employee salary CRUD.
- Salary history.
- Salary components.
- Reports.
- Telegram bot for owner / super admin / configurable users.
- Telegram notification subscriptions.
- Master settings.
- Audit logs.

Future-ready:

- Payroll.
- Overtime.
- Payslips.
- PWA.
- Flutter/mobile API.
- Registered devices.
- Selfie attendance.
- Multi-company / SaaS support.

---

## 3. Core Architectural Principles

### 3.1 QR is not attendance

QR is only an authentication/evidence method.

```text
QR / Kiosk / Mobile / Manual / Future RFID
                   |
                   v
           Attendance Event
                   |
                   v
          Attendance Summary
```

### 3.2 Raw attendance events are immutable

`attendance_events` contains the factual event stream.

Do not silently update or delete raw events when HR performs corrections.

Corrections must:

1. create an approval/correction record;
2. preserve previous data;
3. update the calculated attendance state;
4. write an audit log.

### 3.3 Attendance summary is derived state

`attendances` is the current daily summary derived from events, schedule and approved corrections.

### 3.4 Salary uses effective dates

Never overwrite historical salaries.

Example:

```text
2026-01-01 -> Rp8,000,000
2026-07-01 -> Rp9,000,000
2027-01-01 -> Rp10,000,000
```

Historical payroll/reporting must use the salary record valid for that period.

### 3.5 ID Card QR != Attendance QR

ID card QR is persistent employee verification.

Attendance QR is temporary and short-lived.

Never allow a permanent ID-card QR to become a reusable attendance credential.

### 3.6 Permissions, not hardcoded roles

Application code should preferably check:

```php
$user->can('attendance.correct');
```

instead of:

```php
$user->hasRole('hr');
```

Roles group permissions but business capabilities must be represented by permissions.

---

## 4. Main Domains

```text
Auth
Organization
Employee
Access Control
ID Card
Schedule
Attendance
QR
Location
Leave
Correction
Salary
Payroll
Telegram
Reports
Settings
Audit
```

---

## 5. Recommended Default Roles

- `super-admin`
- `owner`
- `hr-admin`
- `manager`
- `finance`
- `attendance-operator`
- `employee`
- `auditor`

The role list is configurable. Do not encode business behavior by role name.

---

## 6. Important Security Requirements

- QR contains opaque random token, not `user_id`.
- Store QR token hash in DB.
- QR has `issued_at` and `expires_at`.
- QR may be single-use.
- QR can be revoked.
- QR generation must be rate limited.
- Scanner must be authenticated/authorized.
- Geofence validation must occur server-side.
- Store location evidence.
- Store reported GPS accuracy.
- Check maximum allowed location accuracy.
- Protect against duplicate scans / race conditions using DB transaction + locking.
- Salary permissions are separate from employee view permissions.
- Telegram account linking uses temporary pairing token.
- Do not trust Telegram username for identity.
- Sensitive actions must be audited.

---

## 7. Implementation Philosophy

Codex must implement in small, reviewable phases.

For each phase:

1. inspect current repository;
2. do not duplicate existing components;
3. create migrations;
4. create domain models/enums;
5. create services/actions;
6. create policies/permissions;
7. create UI;
8. add tests;
9. update `04_IMPLEMENTATION_STATUS.md`;
10. run tests and formatting.

Avoid giant controllers and business logic embedded in Livewire components.

Recommended pattern:

```text
Controller / Livewire
        |
        v
Action / Service
        |
        v
Domain model / repository / policies
        |
        v
Database
```
