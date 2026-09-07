# Absensi — Database Design

> **Resolved ambiguities.** Six conflicts found by TASK A0 are closed by ADRs in `docs/adr/`.
> Annotations marked **[ADR-000n]** below are binding and override any looser wording nearby.
>
> | ADR | Resolves | Decision |
> |---|---|---|
> | 0002 | C-2 | Permissions grant capability; Policies + query scopes grant row scope |
> | 0003 | C-3 | `attendances` ↔ `attendance_events` FKs added in a third migration |
> | 0004 | C-4 | `attendance_date` = date of the **scheduled shift start** |
> | 0005 | C-5 | Tests run on PostgreSQL; `inet` and `jsonb` retained |
> | 0006 | C-6 | Table is `employee_profiles`; FK column stays `employee_id` |

## 1. General Conventions

Recommended DB: PostgreSQL.

### IDs

Use either:

- bigint PK + UUID public identifier, or
- ULID PK.

Recommended for simplicity:

```text
id              bigint primary key
uuid            uuid unique
```

Use `uuid` in URLs/public references where practical.

### Timestamps

All business timestamps should be timezone-aware at application level.

Recommended DB storage: UTC.  
Display timezone comes from company/settings, default `Asia/Jakarta`.

### Soft deletes

Use selectively.

Recommended soft delete:

- employees
- departments
- positions
- branches
- shifts

Avoid soft-delete semantics for immutable attendance events. Prefer explicit invalidation / correction metadata.

### Money

Never use floating point.

Use:

```text
numeric(18,2)
```

or integer minor units.

Default currency may be IDR but store explicit `currency_code`.

### Database portability **[ADR-0005]**

This schema uses PostgreSQL-native types that SQLite does not implement: `inet`, `jsonb`, and
`numeric(p,s)` with real precision semantics. Laravel's schema builder has no `inet` type, so those
columns require raw DDL.

**The automated test suite therefore runs against PostgreSQL, not SQLite in-memory.** Testing on
SQLite would silently exercise different type semantics than production — worse than not testing.
Do not introduce a SQLite testing path.

---

# 2. Authentication / Access Control

## users

```text
id
uuid
name
email
password
email_verified_at
status
last_login_at
remember_token
created_at
updated_at
```

Indexes:

```text
UNIQUE(email)
UNIQUE(uuid)
INDEX(status)
```

Role/permission tables may follow Spatie defaults.

Recommended permissions:

```text
dashboard.view

users.view
users.create
users.update
users.delete

employees.view
employees.create
employees.update
employees.delete

roles.view
roles.create
roles.update
roles.delete
permissions.view
permissions.assign

id-card.view
id-card.generate
id-card.print
id-card.revoke

attendance.view-own
attendance.view-team
attendance.view-all
attendance.scan
attendance.correct
attendance.approve
attendance.export

schedule.view
schedule.manage

leave.view-own
leave.view-team
leave.view-all
leave.create
leave.approve

salary.view-own
salary.view-team
salary.view-all
salary.create
salary.update
salary.history

payroll.view
payroll.process
payroll.approve
payroll.export

telegram.access
telegram.manage
telegram.notifications
telegram.reports

settings.view
settings.update

audit.view
reports.view
reports.export
```

---

# 3. Organization

## companies

```text
id
uuid
name
legal_name
code
logo_path
timezone
currency_code
status
created_at
updated_at
```

## branches

```text
id
uuid
company_id
name
code
address
phone
email
timezone nullable
status
created_at
updated_at
deleted_at
```

Constraints:

```text
UNIQUE(company_id, code)
```

## departments

```text
id
uuid
branch_id nullable
company_id
parent_id nullable
name
code
status
created_at
updated_at
deleted_at
```

## positions

```text
id
uuid
company_id
department_id nullable
name
code
default_salary nullable
status
created_at
updated_at
deleted_at
```

---

# 4. Employees

## employee_profiles

```text
id
uuid
user_id nullable unique
company_id
branch_id
department_id nullable
position_id nullable
manager_id nullable

employee_number
full_name
phone nullable
gender nullable
birth_date nullable

join_date
end_date nullable
employment_type nullable
employment_status

photo_path nullable

created_at
updated_at
deleted_at
```

Important:

```text
UNIQUE(company_id, employee_number)
```

Do not assume `user_id` is mandatory. Future import/staged employee data may exist before account activation.

## employee_devices

```text
id
uuid
employee_id
device_identifier_hash
device_name nullable
platform nullable
last_used_at nullable
trusted_at nullable
revoked_at nullable
metadata jsonb nullable
created_at
updated_at
```

## employee_documents

```text
id
uuid
employee_id
document_type
name
file_path
expires_at nullable
metadata jsonb nullable
created_at
updated_at
```

---

# 5. ID Cards

## id_card_templates

```text
id
uuid
company_id
name
width_mm
height_mm
front_background_path nullable
back_background_path nullable
config jsonb
is_default
status
created_at
updated_at
```

Recommended default physical size:

```text
85.60mm x 53.98mm
```

## employee_id_cards

```text
id
uuid
employee_id
template_id nullable

card_number
public_token_hash

issued_at
expires_at nullable

status
printed_at nullable

created_by nullable
revoked_by nullable
revoked_at nullable
revoke_reason nullable

created_at
updated_at
```

Statuses:

```text
ACTIVE
EXPIRED
REVOKED
LOST
REPLACED
```

Recommended:

```text
UNIQUE(card_number)
INDEX(employee_id, status)
```

---

# 6. Schedule

## shifts

```text
id
uuid
company_id
branch_id nullable

name
code

start_time
end_time

break_minutes default 0
late_tolerance_minutes default 0
early_checkin_minutes default 60
early_checkout_tolerance_minutes default 0

cross_day boolean default false
status

created_at
updated_at
deleted_at
```

## employee_schedules

```text
id
uuid
employee_id
shift_id nullable
schedule_date
work_mode

status
notes nullable

created_at
updated_at
```

Work mode:

```text
OFFICE
WFH
HYBRID
FIELD
BUSINESS_TRIP
OFF
```

Recommended constraint:

```text
UNIQUE(employee_id, schedule_date)
```

## holidays

```text
id
uuid
company_id
branch_id nullable
holiday_date
name
type
created_at
updated_at
```

---

# 7. Attendance Policies

## attendance_policies

```text
id
uuid
company_id
branch_id nullable
name

qr_enabled
qr_mode
qr_ttl_seconds
qr_refresh_seconds
single_use_qr
anti_replay_enabled

geolocation_required
geofence_required
max_location_accuracy_meters nullable

registered_device_required
selfie_required

late_grace_minutes
early_checkin_minutes

allow_manual_attendance
allow_wfh
allow_field_attendance

is_default
status

created_at
updated_at
```

QR mode:

```text
EMPLOYEE_QR
OFFICE_QR
BOTH
```

---

# 8. QR Sessions

## qr_sessions

```text
id
uuid

employee_id nullable
branch_id nullable

purpose
token_hash

issued_at
expires_at

used_at nullable
revoked_at nullable

generated_by_user_id nullable
generated_ip inet nullable
generated_device_id nullable

metadata jsonb nullable

created_at
```

Purposes:

```text
EMPLOYEE_ATTENDANCE
OFFICE_ATTENDANCE
KIOSK
ID_VERIFICATION_PAIRING
TELEGRAM_PAIRING
```

Timestamps: this table has `created_at` but no `updated_at`, while the row is mutated after creation
(`used_at`, `revoked_at`). The Eloquent model must declare this explicitly — `const UPDATED_AT = null;`
— or the first `->save()` will throw. `attendance_events` is append-only and correctly has `created_at`
only; declare it the same way.

Security:

- never store raw token;
- hash token;
- compare secure hash;
- expire aggressively for attendance;
- mark single-use tokens consumed atomically.

---

# 9. Locations / Geofence

## locations

```text
id
uuid
branch_id
name
address nullable
latitude numeric(10,7)
longitude numeric(10,7)
status
created_at
updated_at
```

## geofences

```text
id
uuid
location_id
name
latitude numeric(10,7)
longitude numeric(10,7)
radius_meters integer
status
created_at
updated_at
```

## employee_attendance_geofences

```text
employee_id
geofence_id
created_at
```

Composite PK or unique:

```text
UNIQUE(employee_id, geofence_id)
```

---

# 10. Attendance

## attendances

Daily/current summary.

```text
id
uuid

employee_id
attendance_date

employee_schedule_id nullable
shift_id nullable

scheduled_check_in_at nullable
scheduled_check_out_at nullable

check_in_at nullable
check_out_at nullable

status

late_minutes default 0
early_leave_minutes default 0
overtime_minutes default 0
worked_minutes default 0

check_in_event_id nullable
check_out_event_id nullable

notes nullable

calculated_at nullable

created_at
updated_at
```

Suggested statuses:

```text
PRESENT
LATE
ABSENT
LEAVE
SICK
PERMISSION
HOLIDAY
OFF
WFH
MISSING_CHECKOUT
```

Uniqueness **[ADR-0004]**:

```text
UNIQUE(employee_id, attendance_date)
```

**Cross-day rule (binding).** `shifts.cross_day` allows a shift to span midnight (e.g. 22:00 Monday
to 06:00 Tuesday), which makes "the attendance date" ambiguous — check-in and check-out fall on
different calendar dates.

```text
attendance_date = the calendar date on which the scheduled shift STARTS.
```

A 22:00 Mon → 06:00 Tue shift produces exactly one `attendances` row dated **Monday**. Both the
check-in event (Mon 22:00) and the check-out event (Tue 06:00) attach to that Monday row. Daily
reports in Phase 8 group by this column and therefore report the night shift under the day it began,
which matches how it is spoken about operationally.

If the business later supports multiple independent shifts per day, replace with:

```text
UNIQUE(employee_id, employee_schedule_id)
```

**Circular foreign key [ADR-0003].** `attendances.check_in_event_id` / `check_out_event_id` reference
`attendance_events`, while `attendance_events.attendance_id` references `attendances`. Declaring both
inline fails on first migrate. Create both tables without the cross constraints, then add both foreign
keys in a third migration. `attendance_events.attendance_id` is nullable; `attendances.check_in_event_id`
is populated by UPDATE inside the same transaction as the event insert.

## attendance_events

Append-only evidence.

```text
id
uuid

employee_id
attendance_id nullable

event_type
event_at
source

qr_session_id nullable
geofence_id nullable

latitude numeric(10,7) nullable
longitude numeric(10,7) nullable
location_accuracy_meters numeric(10,2) nullable

distance_from_geofence_meters numeric(10,2) nullable
geofence_radius_meters integer nullable
geofence_result nullable

ip_address inet nullable
user_agent text nullable
device_identifier_hash nullable

photo_path nullable
metadata jsonb nullable

recorded_by_user_id nullable

created_at
```

Event types:

```text
CHECK_IN
CHECK_OUT
BREAK_START
BREAK_END
MANUAL_CHECK_IN
MANUAL_CHECK_OUT
CORRECTION_APPLIED
```

Sources:

```text
QR
KIOSK
WEB
MOBILE
MANUAL
API
```

---

# 11. Attendance Correction

## attendance_correction_requests

```text
id
uuid

attendance_id
employee_id

requested_by_user_id

correction_type
old_values jsonb
requested_values jsonb
reason

status

reviewed_by_user_id nullable
reviewed_at nullable
review_notes nullable

created_at
updated_at
```

Statuses:

```text
PENDING
APPROVED
REJECTED
CANCELLED
```

---

# 12. Leave

## leave_types

```text
id
uuid
company_id
name
code
paid
quota_enabled
default_quota nullable
status
created_at
updated_at
```

## leave_requests

```text
id
uuid
employee_id
leave_type_id

start_date
end_date
reason

attachment_path nullable

status

reviewed_by_user_id nullable
reviewed_at nullable
review_notes nullable

created_at
updated_at
```

---

# 13. Salary

## employee_salaries

```text
id
uuid

employee_id

effective_from
effective_until nullable

basic_salary numeric(18,2)
currency_code char(3)
payment_frequency

status

created_by_user_id nullable

created_at
updated_at
```

Important rule:

For one employee, effective date ranges must not overlap.

Payment frequency:

```text
MONTHLY
WEEKLY
DAILY
HOURLY
```

## salary_components

```text
id
uuid
company_id

name
code

type
direction

taxable
status

created_at
updated_at
```

Type:

```text
FIXED
VARIABLE
```

Direction:

```text
EARNING
DEDUCTION
```

## employee_salary_components

```text
id
employee_salary_id
salary_component_id
amount numeric(18,2)
metadata jsonb nullable
created_at
updated_at
```

---

# 14. Payroll — Future Phase

## payroll_periods

```text
id
uuid
company_id
name
period_start
period_end
status
locked_at nullable
approved_at nullable
approved_by_user_id nullable
created_at
updated_at
```

## payrolls

```text
id
uuid
payroll_period_id
employee_id

basic_salary
total_earnings
total_deductions
net_salary
currency_code

status
calculation_snapshot jsonb

created_at
updated_at
```

## payroll_items

```text
id
payroll_id
salary_component_id nullable
name
direction
amount
source
metadata jsonb nullable
created_at
```

Payroll must snapshot calculations. Never reconstruct old payslips using current mutable settings.

---

# 15. Telegram

## telegram_users

```text
id
uuid

user_id unique

telegram_user_id bigint unique
telegram_chat_id bigint
telegram_username nullable

status

linked_at
last_activity_at nullable
revoked_at nullable

created_at
updated_at
```

## telegram_pairing_tokens

```text
id
user_id
token_hash
expires_at
used_at nullable
created_at
```

## telegram_notification_subscriptions

```text
id
telegram_user_id

notification_type
delivery_mode

branch_id nullable
department_id nullable

enabled

schedule_time nullable

created_at
updated_at
```

Delivery mode:

```text
IMMEDIATE
DIGEST
OFF
```

## telegram_delivery_logs

```text
id
telegram_user_id nullable

notification_type
payload jsonb

status
telegram_message_id nullable

error_message nullable
sent_at nullable

created_at
```

---

# 16. Settings

## settings

Generic settings only.

```text
id
company_id nullable
scope_type
scope_id nullable
key
value jsonb
created_at
updated_at
```

Scoping: `company_id` and the `scope_type` / `scope_id` pair are redundant — a company-scoped setting
would be representable two ways, guaranteeing inconsistent reads. **Keep the polymorphic pair only**,
drop `company_id`, and add:

```text
UNIQUE(scope_type, scope_id, key)
```

Do not place complex attendance policy rules into this table.

Examples suitable here:

```text
app.name
company.logo
date.format
time.format
default.currency
telegram.bot.enabled
```

---

# 17. Audit

## audit_logs

```text
id
uuid

actor_user_id nullable

action
subject_type
subject_id

old_values jsonb nullable
new_values jsonb nullable

reason nullable

ip_address inet nullable
user_agent text nullable

request_id nullable

created_at
```

Examples:

```text
EMPLOYEE_CREATED
EMPLOYEE_UPDATED
SALARY_UPDATED
ATTENDANCE_CORRECTED
ID_CARD_REVOKED
ROLE_PERMISSION_CHANGED
TELEGRAM_LINKED
SETTING_CHANGED
```

---

# 18. Critical Database Constraints

Codex must add DB-level constraints where practical.

Minimum:

```text
users.email UNIQUE

employee_profiles(company_id, employee_number) UNIQUE

employee_schedules(employee_id, schedule_date) UNIQUE

attendances(employee_id, attendance_date) UNIQUE

employee_id_cards.card_number UNIQUE

telegram_users.user_id UNIQUE
telegram_users.telegram_user_id UNIQUE

salary_components(company_id, code) UNIQUE
```

### Overlap constraints must be enforced by the database

`00` §3.4, §13 and `05` §7 make non-overlapping salary periods a frozen rule, but an application-level
check loses to concurrency — two simultaneous requests both read "no overlap" and both insert. `05` §5
already requires DB-level protection on concurrency-sensitive paths; this is one.

Enable the extension in the Phase 0 migration:

```sql
CREATE EXTENSION IF NOT EXISTS btree_gist;
```

Then enforce:

```sql
ALTER TABLE employee_salaries ADD CONSTRAINT employee_salaries_no_overlap
  EXCLUDE USING gist (
    employee_id WITH =,
    daterange(effective_from, effective_until, '[)') WITH &&
  );
```

The same technique applies to `leave_requests` (per employee, over `daterange(start_date, end_date)`),
which currently has no overlap rule at all.

Concurrency-sensitive operations must use DB transaction + row locking.

Example attendance scan:

```text
BEGIN
  lock qr_session
  validate token
  lock attendance row or create safely
  insert attendance_event
  mark qr used
  recalculate attendance
COMMIT
```

---

# 19. Recommended Indexes

At minimum:

```text
attendance_events(employee_id, event_at)
attendance_events(attendance_id, event_at)
attendances(attendance_date, status)
attendances(employee_id, attendance_date)
qr_sessions(token_hash)
qr_sessions(employee_id, expires_at)
employee_schedules(schedule_date, employee_id)
leave_requests(status, start_date)
employee_salaries(employee_id, effective_from)
audit_logs(subject_type, subject_id, created_at)
audit_logs(actor_user_id, created_at)
telegram_delivery_logs(status, created_at)
```
