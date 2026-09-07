# ADR-0003 — Circular FK between attendances and attendance_events

- **Status:** Accepted
- **Date:** 2026-09-07
- **Resolves:** C-3 (TASK A0)

## Context

`02_DATABASE_DESIGN.md` §10 defines a mutual reference:

```text
attendances.check_in_event_id   -> attendance_events.id
attendances.check_out_event_id  -> attendance_events.id
attendance_events.attendance_id -> attendances.id   (nullable)
```

Neither table can be created first with its foreign keys declared inline. A naive migration pair
fails on the first `php artisan migrate` — before any Phase 4 logic is even reachable.

The cycle is not a modelling error. It is the intended design: `attendance_events` is the append-only
evidence layer (`00` §3.2) and `attendances` is derived state (`00` §3.3) that must point at the two
events which produced it.

## Decision

Three migrations, in order:

1. `create_attendances_table` — no FK to `attendance_events`; `check_in_event_id` and
   `check_out_event_id` are nullable `unsignedBigInteger`.
2. `create_attendance_events_table` — no FK to `attendances`; `attendance_id` is a nullable
   `unsignedBigInteger`.
3. `add_attendance_event_foreign_keys` — adds all three constraints.

Write order inside the scan transaction:

```text
BEGIN
  lock qr_session, validate, mark consumed
  SELECT ... FOR UPDATE  or create the attendances row
  INSERT attendance_event  (attendance_id set)
  UPDATE attendances SET check_in_event_id = <new event id>
  recalculate summary
COMMIT
```

`attendance_events.attendance_id` stays nullable permanently: an event may legitimately arrive before
its summary row is resolvable (unknown schedule, employee with no schedule that day). The event is
still valid evidence and must be recorded — it is reconciled later.

Deletion: `attendance_events` FKs are `ON DELETE RESTRICT`. Evidence is immutable per `00` §3.2 and
must not cascade away with a summary row.

## Consequences

- Migration count grows by one; ordering becomes load-bearing and is documented here.
- Both FK directions are enforced at the database level, not by application convention.

## Alternatives rejected

**PostgreSQL `DEFERRABLE INITIALLY DEFERRED` constraints.** Would allow a single transaction to
satisfy both directions. Rejected: Laravel's schema builder has no first-class support, it needs raw
DDL anyway, and it makes constraint violations surface at COMMIT rather than at the offending
statement — materially harder to debug in the highest-risk module in the system.

**Dropping `check_in_event_id` / `check_out_event_id` and deriving them by query.** Loses the explicit
link that makes a correction auditable, and adds a subquery to every attendance read. Rejected.

## Reversibility

High cost after Phase 4 ships — changing FK direction means migrating live evidence data.
