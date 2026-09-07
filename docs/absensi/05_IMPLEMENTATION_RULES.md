# Absensi — Codex Implementation Rules

These rules are mandatory for AI-assisted implementation.

## 1. Read Before Editing

Before implementation, Codex must inspect:

1. repository structure;
2. `composer.json`;
3. Laravel version;
4. PHP requirement;
5. existing auth implementation;
6. existing models/migrations;
7. existing UI stack;
8. existing tests;
9. coding conventions;
10. current git diff.

Do not assume a blank project if code already exists.

---

## 2. Do Not Rewrite Unrelated Code

Only modify files necessary for the active roadmap phase.

Avoid:

- mass refactors;
- unrelated formatting changes;
- dependency upgrades without need;
- renaming existing stable modules unnecessarily.

---

## 3. Domain Logic Placement

Avoid giant controllers.

Recommended:

```text
app/
  Domain/
    Attendance/
      Actions/
      DTOs/
      Enums/
      Events/
      Models/
      Services/
      Policies/
```

If the existing project already follows another clean convention, integrate with it instead of forcing this exact directory tree.

---

## 4. Attendance Invariants

### QR

- opaque token;
- cryptographically random;
- hash stored server-side;
- short TTL;
- atomic consume;
- rate-limited generation;
- revocable.

### Event

Attendance event is evidence and should be append-oriented.

Never overwrite an original check-in merely to satisfy a correction.

### Summary

Daily attendance may be recalculated from:

- schedule;
- attendance events;
- leave;
- approved correction;
- attendance policy.

---

## 5. Concurrency

Attendance scans are concurrency-sensitive.

Use database transaction and locking.

Test:

- two simultaneous scans of same QR;
- QR used after expiry;
- QR used after revoke;
- same user duplicate check-in;
- scanner retries due to network latency.

A valid single-use QR must not produce two accepted events.

---

## 6. Authorization

Do not rely on hidden buttons alone.

Every protected backend action needs authorization.

Examples:

```text
salary.view-all
attendance.correct
id-card.revoke
telegram.manage
settings.update
```

Sensitive data must never be returned before authorization.

---

## 7. Salary Rules

- Money uses decimal/integer representation.
- Salary history is effective-dated.
- Avoid overlapping effective periods.
- Salary update creates/adjusts history; do not mutate old payroll truth.
- Salary changes are audited.
- Employee profile permission does not automatically expose salary.

---

## 8. Telegram Rules

- Telegram user must pair with application user.
- Pairing token expires.
- Pairing token is single-use.
- Telegram username is metadata only.
- Commands require app permission.
- Notification delivery goes through queue.
- Telegram failure must not fail attendance recording.
- Delivery attempts are logged.

---

## 9. Geofence Rules

- Location verification occurs on server-side.
- Store coordinates captured from client.
- Store accuracy.
- Store calculated distance.
- Store matched geofence.
- Store configured radius at event time if possible.
- Do not claim GPS is tamper-proof.
- WFH/FIELD policies can alter geofence requirements.

---

## 10. ID Card Rules

- ID card supports revoke/lost/replace.
- Public verification reveals minimal employee data.
- ID card QR never directly authorizes attendance.
- Printing should support CR80 dimensions.
- PDF output should be deterministic enough for print.

---

## 11. Audit Rules

Audit at least:

- salary create/update;
- attendance correction;
- role permission change;
- settings update;
- ID card revoke;
- Telegram pair/revoke;
- manual attendance;
- payroll approval/lock.

Record:

```text
actor
action
subject
before
after
timestamp
IP
request identifier if available
```

---

## 12. Testing Rules

Each feature requires tests.

Minimum attendance tests:

- valid check-in;
- valid check-out;
- expired QR;
- used QR;
- revoked QR;
- invalid QR;
- unauthorized scanner;
- outside geofence;
- poor GPS accuracy;
- valid geofence;
- late calculation;
- cross-day shift;
- duplicate scan;
- concurrent scan.

Minimum salary tests:

- salary create;
- salary effective date;
- historical salary preserved;
- overlap rejected;
- unauthorized salary access rejected.

Minimum Telegram tests:

- valid pair;
- expired pair token;
- unauthorized command;
- scoped notifications;
- queue dispatch.

---

## 13. Documentation Update

After each implementation task:

- update `04_IMPLEMENTATION_STATUS.md`;
- note changed files;
- note test command/result;
- note unresolved issues;
- update roadmap checkbox if completed.

---

## 14. No Fake Completion

Never mark a task DONE because files were created.

DONE means:

- feature works;
- authorization works;
- validation works;
- tests pass;
- documentation updated.

If partially implemented, mark `PARTIAL`.

---

## 15. Git Discipline

Before editing:

```bash
git status
git diff
```

After editing:

```bash
git diff --check
php artisan test
```

Also run project-specific formatting/static-analysis commands when available.

Do not delete user changes not related to the task.
