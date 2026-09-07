# ADR-0005 — Test database engine is PostgreSQL

- **Status:** Accepted
- **Date:** 2026-09-07
- **Resolves:** C-5 (TASK A0)

## Context

`02_DATABASE_DESIGN.md` specifies PostgreSQL-native types:

- `inet` — `attendance_events.ip_address`, `qr_sessions.generated_ip`, `audit_logs.ip_address`
- `jsonb` — metadata, `old_values`/`new_values`, `calculation_snapshot`, settings values
- `numeric(10,7)` — latitude/longitude
- `EXCLUDE USING gist` — salary period non-overlap (see `02` §18)

Laravel's schema builder has **no `inet` type**; those columns need raw DDL. SQLite has no `inet` at
all, treats `numeric(10,7)` as loosely-typed REAL, has no `jsonb` operators, and no exclusion
constraints.

The Laravel default of `:memory:` SQLite for tests would therefore either fail outright or — worse —
pass while exercising different semantics than production. A green suite that does not test the
production type system is a false signal, and this project's riskiest logic (geofence distance
arithmetic, JSONB audit diffs, salary overlap rejection) is exactly the logic SQLite would not
faithfully execute.

## Decision

**The automated test suite runs against a real PostgreSQL database.** `inet` and `jsonb` are retained.

- A dedicated `absensi_test` database, `RefreshDatabase`.
- **PostgreSQL 17** provided by `docker-compose.yml` for local work and by a service container in CI.
- `phpunit.xml` sets `DB_CONNECTION=pgsql` and `DB_DATABASE=absensi_test` explicitly, so a stray
  local `.env` cannot silently redirect the suite.
- **No SQLite testing path is to be introduced**, including "fast" partial suites. A second engine
  means two type systems to reason about and reintroduces exactly the divergence this avoids.

## Consequences

- Tests require a running PostgreSQL. This is the cost, and it is real: contributors cannot run the
  suite with zero setup, and CI needs a service container.
- Test runs are slower than in-memory SQLite. Mitigate with transactional rollback rather than
  migrating per test, never by switching engine.
- Phase 5 geofence maths and Phase 7 salary overlap can be tested against the constraints that
  actually run in production.

## Amendment 2026-09-07 — server version pinned to 17

P0-3 found PostgreSQL **14.18** on the development machine (Homebrew), and the original draft of this
ADR said "PostgreSQL 16" without justifying the number.

The host version is not usable as the project baseline:

- `psql --version` reports the client, not the server. The server version is what constrains the schema.
- **PostgreSQL 14 goes end-of-life in November 2026**, about fourteen months out. This project reaches
  payroll (Phase 10) well after that. Building on a version whose support ends mid-project forces the
  upgrade to happen during the money-handling phases instead of before Phase 1.
- Capability is *not* the issue — `btree_gist`, `EXCLUDE USING gist`, `daterange`, `jsonb` and `inet`
  all work on 14. This is purely support lifetime, which is exactly the kind of cost that is trivial
  now and disruptive later.

**Decision:** target **PostgreSQL 17** (supported to November 2029) via `docker-compose.yml`, used
identically in local development, test and CI, so all three run one server version. Do not use the
Homebrew PostgreSQL 14 for this project; leave it installed and untouched for whatever else needs it,
and use the container's `psql` to avoid a 14 client against a 17 server.

If the eventual production target is a managed PostgreSQL pinned to a different major, change this
number to match production and re-pin the container — a dev/prod major mismatch is the failure this
amendment exists to prevent.

## Status against the installed project — 2026-09-07

The installed starter kit **violates this ADR as shipped**:

```xml
<!-- phpunit.xml, as installed -->
<env name="DB_CONNECTION" value="sqlite"/>
<env name="DB_DATABASE" value=":memory:"/>
```

This is exactly the configuration rejected above, and it is the starter kit's default rather than a
deliberate choice. It must be repointed at the PostgreSQL 17 container before any business migration
is written. Tracked as **R-1** in `REPOSITORY_AUDIT.md` §11.3.

Consequence once fixed: `docker compose up -d` becomes a prerequisite for running the test suite.
That is the accepted cost of this decision, not a regression.

## Alternatives rejected

**Replace `inet` with `varchar(45)`** (wide enough for IPv6) to restore SQLite portability. Genuinely
viable, and it was considered seriously — it costs only PostgreSQL's native address validation. Rejected
because it buys portability this project never uses: `jsonb`, `numeric(10,7)` and the `EXCLUDE` constraint
still rule SQLite out, so the concession would be paid without obtaining the benefit.

**SQLite for unit tests, PostgreSQL for feature tests.** The split has to be policed forever, and the
first test placed on the wrong side fails silently. Rejected.

## Reversibility

Cheap to reverse in Phase 0, expensive after a large suite exists.
