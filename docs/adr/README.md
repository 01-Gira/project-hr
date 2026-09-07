# Architecture Decision Records

Each ADR records one decision, the reasoning behind it, the alternatives rejected and why, and how
expensive it is to reverse.

**Accepted** decisions are binding. `docs/absensi/` is annotated where an ADR overrides it.
**Proposed** decisions carry the reasoning but are not yet binding, and are marked with the phase
gate by which they must be settled.

| ADR | Decision | Status | Resolves |
|---|---|---|---|
| [0001](0001-documentation-layout.md) | Documentation layout: `docs/absensi/`, `docs/adr/`, `docs/implementation/` | Accepted | C-1 |
| [0002](0002-authorization-model.md) | Permissions grant capability; Policies + query scopes grant row scope | Accepted | C-2 |
| [0003](0003-circular-foreign-keys.md) | `attendances` ↔ `attendance_events` FKs added in a third migration | Accepted | C-3 |
| [0004](0004-cross-day-attendance-date.md) | `attendance_date` = date of scheduled shift start | Accepted — confirm before Phase 3 | C-4 |
| [0005](0005-test-database-engine.md) | Tests run on PostgreSQL; `inet`/`jsonb` retained; no SQLite path | Accepted | C-5 |
| [0006](0006-employee-table-naming.md) | Table `employee_profiles`; FK stays `employee_id`; relations declared explicitly | Accepted | C-6, C-7 |
| [0007](0007-frontend-stack.md) | Blade + Livewire 3 + Alpine + Tailwind; no Filament/Nova | Proposed — before P0-4 | pending decision |
| [0008](0008-identifier-strategy.md) | `bigint` PK + `uuid` (v7) public identifier | Proposed — before P0-4 | pending decision |
| [0009](0009-multi-company-scope.md) | Multi-company: schema-only readiness in V1 | Proposed — before P0-4 | pending decision |
| [0010](0010-salary-visibility-default.md) | `salary.view-own` not granted to employees by default | Proposed — before P0-8 | pending decision |

## Still open, deliberately

Package choices are deferred to the phase that consumes them, so they are made against the
Laravel/PHP compatibility that exists at that time: QR (Phase 4), PDF (Phase 2), map provider
(Phase 5), Telegram SDK (Phase 9), Excel export (Phase 8).
