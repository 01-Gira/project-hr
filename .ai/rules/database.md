# Database

**Globs:** `database/migrations/**`, `config/database.php`, `phpunit.xml`

## PostgreSQL only, including tests

The schema uses `inet`, `jsonb`, `numeric(10,7)` and `EXCLUDE USING gist`.
SQLite implements none of them, so a green SQLite suite would prove nothing
about production. `phpunit.xml` targets PostgreSQL and `docker compose up -d` is
a prerequisite for running tests. **Do not introduce a SQLite testing path**,
including a "fast" partial one. See ADR-0005.

## Money is never a float

`numeric(18,2)` or integer minor units, with an explicit `currency_code`.
`bcmath` is a required extension. See `02_DATABASE_DESIGN.md` §1.

## Store UTC

`APP_TIMEZONE=UTC`. Display timezone is resolved from company settings, default
`Asia/Jakarta`. Never set the application timezone to a local zone: it writes
local times into columns the rest of the system reads as UTC, and the corruption
only surfaces later, in attendance reports.

## Overlap rules belong in the database

Non-overlapping effective-dated salary periods are enforced with
`EXCLUDE USING gist` over a `daterange`, not an application check. Two concurrent
inserts both read "no overlap" and both succeed. `btree_gist` is enabled by
migration. Same technique for `leave_requests`.

## attendances and attendance_events reference each other

Create both tables without the cross constraints, then add the foreign keys in a
third migration. Declaring them inline fails on first migrate.
`attendance_events.attendance_id` stays nullable — an event may arrive before its
summary row is resolvable, and it is still valid evidence. Evidence foreign keys
are `ON DELETE RESTRICT`. See ADR-0003.

## attendance_date is the scheduled shift's start date

A 22:00→06:00 shift produces one `attendances` row dated the day it **began**.
Resolve it by schedule lookup, never by truncating the event timestamp. See
ADR-0004.

## Naming

The table is `employee_profiles`; the foreign key is `employee_id`. Because these
disagree, **every relationship must name its key explicitly** —
`belongsTo(EmployeeProfile::class, 'employee_id')`. Larastan level 7 catches the
inference failure. See ADR-0006.

## Mirror a column default into $attributes

A database default is applied at INSERT, not at instantiation, so a freshly
created model reports `null` without a mirror in `$attributes`. If you mirror
one, assert that the two definitions still agree — they drift silently.
