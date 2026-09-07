# Domain invariants

**Globs:** `app/Domain/**`, `app/Models/**`, `app/Actions/**`

These are frozen decisions from `04_IMPLEMENTATION_STATUS.md`. Changing one
requires an explicit architecture decision, not a refactor.

## QR is not attendance

QR is one authentication method that produces an attendance event. Never couple
attendance to QR. Sources include kiosk, web, mobile, manual and API.

## QR tokens are opaque, hashed, short-lived and single-use

Never store a raw token. Never encode `user_id` in one. Generation is rate
limited; consumption is atomic under a transaction with row locking. A valid
single-use QR must never produce two accepted events.

## An ID card QR is not an attendance QR

ID card QR is persistent verification. A permanent card token must never act as
a reusable attendance credential.

## attendance_events is the evidence layer

Append-only. A correction never overwrites the original event: it creates a
correction record, preserves the previous values, updates the derived state and
writes an audit entry.

`attendances` is derived state, recalculated from schedule, events, leave,
approved corrections and policy.

## Salary history is effective-dated

Never overwrite a historical salary. Effective ranges must not overlap, enforced
by the database. Payroll snapshots its calculation; never reconstruct an old
payslip from current mutable settings.

## Geofence validation is server-side

Store the coordinates, the reported accuracy, the computed distance, the matched
geofence and the radius in force at the time. Do not treat GPS as tamper-proof.

## Telegram inherits application permissions

A Telegram identity is linked to an application user through a single-use,
expiring pairing token. The username is metadata, never identity. Delivery is
queued, and a Telegram failure must never fail attendance recording.
