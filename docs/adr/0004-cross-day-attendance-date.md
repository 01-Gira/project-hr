# ADR-0004 — attendance_date for cross-day shifts

- **Status:** Accepted — **owner should confirm before Phase 3**
- **Date:** 2026-09-07
- **Resolves:** C-4 (TASK A0)

## Context

`02_DATABASE_DESIGN.md` §10 mandates `UNIQUE(employee_id, attendance_date)`. §6 defines
`shifts.cross_day boolean`. `03_ROADMAP.md` Phase 3 requires cross-day shift support and
`05_IMPLEMENTATION_RULES.md` §12 requires a cross-day shift test.

For a 22:00 Monday → 06:00 Tuesday shift, check-in and check-out fall on different calendar dates.
**The blueprint never says which date owns the row.** Two implementers will choose differently, and
the unique constraint means the disagreement corrupts data rather than merely looking odd: a night
worker could produce two rows for one shift, or collide with the following day's shift.

## Decision

```text
attendance_date = the calendar date on which the SCHEDULED SHIFT STARTS.
```

Derived from `employee_schedules.schedule_date`, never from the wall-clock time of the check-in event.
An employee arriving at 00:15 for a shift scheduled to start 22:00 the previous day still books
against the previous day.

`UNIQUE(employee_id, attendance_date)` is retained for V1.

Consequences that follow and must be implemented as stated:
- Resolving which schedule an event belongs to is a **schedule lookup**, not a date truncation. The
  resolver searches schedules whose computed `[start - early_checkin_minutes, end + grace]` window
  contains the event timestamp.
- Phase 8 daily reports group by `attendance_date`, so a night shift is reported under the day it
  began.
- An employee may not hold two schedules whose windows overlap. Phase 3 validation must reject that,
  otherwise event-to-schedule resolution is ambiguous.

## Why this option

The alternative — dating by check-in wall clock — is simpler to compute and wrong in the case that
matters. It splits a single shift across two dates whenever someone clocks in after midnight, which
is precisely the normal case for a night shift, and it makes "how many people worked Monday night"
unanswerable without reconstructing shift windows at query time.

Scheduled-start dating costs one schedule lookup that Phase 4 already has to perform in order to
compute lateness at all. There is no extra work.

## Alternatives rejected

**`UNIQUE(employee_id, employee_schedule_id)`** — the escape hatch offered in `02` §10. Strictly more
general and correctly handles multiple independent shifts per day. Rejected **for V1 only**, because
it complicates every Phase 8 report (no date column to group by; must join schedules) for a
requirement that does not exist yet. Adopt it the day split shifts become real — the migration is a
constraint swap plus a report rewrite, and this ADR should be superseded rather than amended.

**Dating by check-out.** Same defect as check-in dating, and additionally undefined until the employee
checks out, so `MISSING_CHECKOUT` rows would have no date at all. Rejected.

## Confirmation requested

This is a business-semantics decision made from the blueprint alone. It is cheap to reverse now and
expensive after Phase 4 stores production evidence. If night shifts are expected to be reported under
the date they *end*, say so before Phase 3.
