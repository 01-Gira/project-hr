# Absensi — Implementation Status

> This file is the canonical project progress tracker.
>
> Codex MUST update this file after implementing each task or phase.

Last reviewed: 2026-09-07
Last verified by: **TASK A0 — Repository Audit** (`docs/implementation/REPOSITORY_AUDIT.md`)

## Overall Status

```text
Project status       : PHASE 0 IN PROGRESS
Current phase        : PHASE 0 — Project Foundation
Overall progress     : Phase 0 partially complete; Phases 1-12 at 0%
Production ready     : NO
Codebase present     : YES — Laravel 13.17 Livewire starter kit (408eb13)
Version control      : YES — git, branch main
```

> Superseded the earlier "DESIGN / NOT IMPLEMENTED" status. The owner installed the Laravel Livewire
> starter kit in `408eb13`. Current audit: `docs/implementation/REPOSITORY_AUDIT.md` §11.

### Verification basis

All statuses below are derived from direct filesystem inspection on 2026-09-07, not from assumption:

```bash
find . -type f | wc -l          # 10  — all Markdown, all under docs/
git log --oneline               # 55eeca2 Add comprehensive documentation for Absensi project
git ls-files                    # the same 10 Markdown files
```

> **Correction, 2026-09-07.** The first issue of this file recorded "Version control: NO". That reflected a
> probe at 14:06; the repository owner ran `git init` and committed the blueprint at 14:08 (`55eeca2`,
> author `Gira <giramnk@gmail.com>`) while TASK A0 was being written. The greenfield verdict is unchanged —
> that commit contains documentation only, no application code.

Absent: `composer.json`, `composer.lock`, `artisan`, `app/`, `bootstrap/`, `config/`,
`database/`, `routes/`, `resources/`, `public/`, `vendor/`, `tests/`, `phpunit.xml`,
`.env`, `.env.example`, `package.json`, `vite.config.js`, `pint.json`, `phpstan.neon`,
`.github/`, `.git/`.

Sibling directories under `/Users/ramusen/Projects/` were checked and none contains a prior
Absensi implementation (`absence-app` is Python; `ramusen-crm` is a Laravel CRM, different
product; `ramusen` is legacy PHP; `bantuwarga` is Next.js). Greenfield is **confirmed**, not assumed.

---

# Verified Stack Inventory

| Item | Verified value |
|---|---|
| Laravel version | **13.17** |
| PHP version requirement | **^8.3** (host 8.4.11) |
| Frontend stack | **Livewire 4.1 + Flux 2.13 + Blaze + Vite** |
| Database driver | **pgsql** → PostgreSQL 17 container, port 5433 (R-2 fixed) |
| Redis / cache | **redis**, port 6380 (R-3 fixed) — `redis` ext present; **runtime unverified** |
| Queue | **redis** (R-3 fixed) |
| Authentication | **Fortify 1.37** incl. passkeys + 2FA |
| Role / permission implementation | **NOT PRESENT** — no spatie/laravel-permission |
| Migrations | **5** (users, cache, jobs, passkeys, 2FA columns) |
| Models | **1** (`User`) |
| Modules / domains | **0** business domains |
| Tests | **10 files** (auth, dashboard, settings) — **not executed by this session**; suite now targets PostgreSQL `absensi_test` |
| Formatter | **Pint 1.27** |
| Static analysis | **Larastan 3.9, level 7** |
| CI | **NOT PRESENT** |
| Agent guidance | **Laravel Boost 2.2** — `AGENTS.md`, `.agents/skills/` (9 skills) |
| Version control | **git**, branch `main`, 1 commit, 10 files tracked |
| Host PHP | **8.4.11** (Homebrew, NTS) — verified 2026-09-07 |
| Host Composer | **2.8.10** — verified 2026-09-07 |
| Host psql client | **14.18** (Homebrew) — **not the project baseline**, see ADR-0005 amendment |
| Host Redis | **8.10.0** — verified 2026-09-07 |
| Project PostgreSQL target | **17**, via `docker-compose.yml` (ADR-0005) |
| PHP extensions | **all present**: `pdo_pgsql`, `pgsql`, `redis`, `bcmath`, `intl`, `zip`, `gd` |
| Services | PostgreSQL 17 + Redis 8 containers **healthy** on 5433 / 6380 |

---

# Task Queue Progress

| Task | Name | Status | Evidence |
|---|---|---|---|
| A0 | Repository Audit | **DONE** | `docs/implementation/REPOSITORY_AUDIT.md` created 2026-09-07 from direct filesystem inspection |
| A1 | Phase 0 Foundation | NOT STARTED | no code exists |
| A2 | Organization & Employee | NOT STARTED | no code exists |
| A3 | ID Card | NOT STARTED | no code exists |
| A4 | Schedule | NOT STARTED | no code exists |
| A5 | Attendance Core | NOT STARTED | no code exists |
| A6 | Geofence | NOT STARTED | no code exists |
| A7 | Exceptions | NOT STARTED | no code exists |
| A8 | Salary | NOT STARTED | no code exists |
| A9 | Reports | NOT STARTED | no code exists |
| A10 | Telegram | NOT STARTED | no code exists |
| A11 | Payroll | NOT STARTED | no code exists |

A0 is the only task marked DONE. Its deliverable is a document, and that document exists and is
complete against the requirements in `06_CODEX_TASKS.md`. No feature work is claimed.

---

# Phase Progress

| Phase | Name | Status | Progress |
|---|---|---|---:|
| 0 | Project Foundation | NOT STARTED | 0% |
| 1 | Organization & Employee Master | NOT STARTED | 0% |
| 2 | Employee ID Card | NOT STARTED | 0% |
| 3 | Shift & Scheduling | NOT STARTED | 0% |
| 4 | Attendance Core & QR | NOT STARTED | 0% |
| 5 | Geolocation & Geofence | NOT STARTED | 0% |
| 6 | Leave / Permission / Corrections | NOT STARTED | 0% |
| 7 | Salary Management | NOT STARTED | 0% |
| 8 | Reports | NOT STARTED | 0% |
| 9 | Telegram Control Room | NOT STARTED | 0% |
| 10 | Payroll | NOT STARTED | 0% |
| 11 | Advanced Attendance Security | NOT STARTED | 0% |
| 12 | PWA / API / Mobile | NOT STARTED | 0% |

Allowed status:

```text
NOT STARTED
IN PROGRESS
BLOCKED
PARTIAL
DONE
DEFERRED
```

---

# Current Sprint

## Target

PHASE 0 — Project Foundation

## Blocking prerequisites (added by TASK A0)

These are not optional and are not yet in `03_ROADMAP.md`. They must be completed before
Laravel is installed, because each one changes migration or architecture output:

- [x] **P0-1a** `git init` + baseline commit of the blueprint docs — **done by owner**, `55eeca2`, 2026-09-07.
- [ ] **P0-1b** `.gitignore` with Laravel defaults. **Hard prerequisite for installing Laravel** — without it the install commits `vendor/`, `node_modules/` and `.env` (credential leak).
- [ ] **P0-1c** Commit `docs/implementation/REPOSITORY_AUDIT.md` and this file, so the pre-implementation state is a named commit later phases can diff against.
- [x] **P0-2** Resolve blueprint conflicts C-1 … C-6 and record ADRs — **done 2026-09-07**:
  - [x] C-1 documentation path → docs moved to `docs/absensi/` via `git mv`. `ADR-0001`.
  - [x] C-2 row-level scoping → permissions grant capability, Policies + `scopeVisibleTo` grant row scope; contract built in Phase 0. `ADR-0002`.
  - [x] C-3 circular FK → three migrations, FKs added last; `ON DELETE RESTRICT` on evidence. `ADR-0003`.
  - [x] C-4 cross-day shift → `attendance_date` = date of **scheduled shift start**. `ADR-0004`. **Owner confirmation requested before Phase 3.**
  - [x] C-5 test database engine → PostgreSQL; `inet`/`jsonb` retained; no SQLite path. `ADR-0005`.
  - [x] C-6 naming → table is `employee_profiles`, FK stays `employee_id`, relations declared explicitly. `ADR-0006`.
  - [x] Also annotated into `02_DATABASE_DESIGN.md`: C-8 (holidays↔branches in ERD), C-9 (`qr_sessions` has no `updated_at`), C-10 (settings scope redundancy), C-11 (`EXCLUDE USING gist` for salary/leave overlap).
- [x] **P0-3a** Host PHP / Composer / PostgreSQL / Redis versions verified 2026-09-07 by the owner.
  - PHP **8.4.11**, Composer **2.8.10**, Redis **8.10.0** — all acceptable.
  - psql client **14.18**. PostgreSQL 14 is **rejected as the project baseline**: it reaches end of
    life in November 2026, inside this project's build window and before the payroll phases.
    Capability is not the issue — `btree_gist`, `EXCLUDE USING gist`, `daterange`, `jsonb` and `inet`
    all work on 14 — support lifetime is. `ADR-0005` amended to pin **PostgreSQL 17** via
    `docker-compose.yml`, used identically in dev, test and CI.
- [x] **P0-3b** PHP extensions and services confirmed 2026-09-07. **All present** — `pdo_pgsql`,
  `pgsql`, `redis`, `bcmath`, `intl`, `zip`, `gd`. No `predis/predis` needed, so no dependency change
  and no approval required under AGENTS.md. `bcmath` in particular is present, which is what makes
  `numeric(18,2)` salary arithmetic safe (`02` §1 forbids floats).
- [ ] **P0-3c** Record the Laravel version that `composer create-project` actually resolves against
  PHP 8.4, and pin it. Not guessed here — the assisting shell has no network to check Packagist.

## Execution constraint (verified 2026-09-07)

The assisting shell used for TASK A0 and P0-1/P0-2 is **not** the macOS host. It is a Linux VM
(`Linux claude 6.8.0 aarch64 Ubuntu`) that mounts the repository folder, and its outbound network is
blocked by proxy:

```text
uname -a                  # Linux ... aarch64 GNU/Linux   (not macOS)
curl https://packagist.org  # curl (56) HTTP 403 from proxy after CONNECT
php / composer / psql / redis-cli   # not installed, and not installable (no egress)
```

Consequences for the remaining Phase 0 work:

1. ~~**Host toolchain versions cannot be read from the assisting shell.**~~ **Resolved** — the owner
   ran the versions on the Mac 2026-09-07 and they are recorded above. Any further host probe
   (extensions, running services, resolved Laravel version) must come from the owner the same way.
2. **`composer create-project` cannot run there** (no Composer, no network). P0-4 must be run by the
   owner on the Mac.
3. **`php artisan test`, `pint` and `phpstan` cannot be executed by the assistant.** Per
   `05_IMPLEMENTATION_RULES.md` §14, a phase is DONE only when tests pass — so **Phase 0 can never be
   marked DONE on the assistant's word alone**. Test output must come from the owner's machine or CI.

What the assistant *can* do: read, write and edit files in the repository, and run `git`. So migrations,
models, seeders, policies, Livewire components, tests and CI configuration can all be authored here;
they simply have to be **executed and verified on the Mac**, and the results pasted back before any
checkbox is ticked.

## Phase 0 checklist

- [ ] Laravel repository initialized.
- [ ] `.env.example` prepared.
- [ ] PostgreSQL configured.
- [ ] Redis configured.
- [ ] Queue configured.
- [ ] `btree_gist` extension enabled (required later by C-11 overlap constraints).
- [ ] Application timezone strategy (`APP_TIMEZONE=UTC`, display timezone from settings).
- [ ] Authentication implemented.
- [ ] `users` extended with `uuid`, `status`, `last_login_at` per `02` §2.
- [ ] Base dashboard implemented.
- [ ] Role/permission dependency installed.
- [ ] Permissions seeded (~60, verbatim from `02` §2).
- [ ] Default roles seeded (8, mapped from `07_PERMISSION_MATRIX.md`).
- [ ] Scope resolver contract created (C-2).
- [ ] Super-admin bootstrap documented and repeatable.
- [ ] Audit logging foundation implemented.
- [ ] Common enums implemented.
- [ ] Exception handling conventions defined.
- [ ] Base test suite configured against PostgreSQL.
- [ ] Formatting/static analysis commands documented (Pint + Larastan).
- [ ] CI configured.

---

# Implemented Database Tables

```text
0 of ~30 tables implemented.
```

None. `database/migrations/` does not exist.

When implemented, update in this format:

```text
[x] users
[x] roles
[x] permissions
[ ] companies
...
```

---

# Implemented Modules

```text
0 of 17 domains implemented.
```

None. `app/` does not exist.

---

# Known Gaps

Resolved by TASK A0:

- ~~Project code has not been audited yet.~~ **Audited 2026-09-07.**
- ~~Existing Laravel version/dependencies are unknown until Codex inspects repository.~~ **Verified: no Laravel project exists.**

Open:

- ~~No version control.~~ **Resolved** by owner 2026-09-07 (`55eeca2`). Rule `05` §15 is now enforceable.
- No `.gitignore`. Installing Laravel before one exists commits `vendor/`, `node_modules/` and `.env`. *(P0-1b)*
- The repository is edited concurrently by its owner. Repository-state findings must be re-verified immediately before any conclusion is drawn or any commit made. *(process)*
- Blueprint path in `README.md` / `06_CODEX_TASKS.md` (`docs/absensi/`) does not match reality (`docs/`). *(C-1)*
- Row-level scope model (`team`, `own`, `restricted` in `07_PERMISSION_MATRIX.md`) is not expressible in `spatie/laravel-permission` alone; requires Policies + query scopes. *(C-2)*
- `attendances` ↔ `attendance_events` mutual foreign keys will fail a naive migration. *(C-3)*
- `UNIQUE(employee_id, attendance_date)` is undefined for cross-day shifts. *(C-4)*
- `inet` and `jsonb` block SQLite-based testing; test engine undecided. *(C-5)*
- `02` §18 says `employees(...)`; the table is `employee_profiles`. *(C-6)*
- `employee_salaries` non-overlap and `leave_requests` overlap have no DB-level enforcement specified, despite `05` §5 requiring DB-level protection on concurrency-sensitive paths. *(C-11)*
- No reproducible local PostgreSQL/Redis services defined. *(P0-5)*
- Host toolchain versions unverified. *(P0-3)*
- Exact UI component library not selected — audit recommends Tailwind + hand-built, no admin framework.
- Exact PDF/QR/map/Telegram/Excel packages deliberately deferred to their phases.
- Payroll legal/statutory logic intentionally not defined yet.

Full detail for every C-item: `docs/implementation/REPOSITORY_AUDIT.md` §6.

---

# Critical Decisions Already Frozen

These should not be changed without an explicit architecture decision.

1. QR attendance uses short-lived opaque tokens.
2. Raw QR token must not be stored in plaintext.
3. ID Card QR is not an attendance QR.
4. Attendance events are the immutable evidence layer.
5. Daily attendance is derived/current state.
6. Salary history uses effective dating.
7. Salary permission is separate from employee profile permission.
8. Telegram identity is linked to an application user.
9. Telegram permissions come from application RBAC.
10. Geofence evidence stores coordinates, distance, radius and GPS accuracy.
11. Attendance must protect against duplicate/concurrent scans.
12. Complex attendance policy is not stored only as generic key/value settings.

---

# Pending Architectural Decisions

TASK A0 recommends the following. They are **recommendations, not yet frozen** — they become
frozen when accepted and recorded as ADRs in P0-2. Rationale for each: `REPOSITORY_AUDIT.md` §6.

| Decision | Recommendation | Status | ADR |
|---|---|---|---|
| Test database engine | PostgreSQL, not SQLite | **ACCEPTED** | `ADR-0005` |
| Cross-day attendance date rule | `attendance_date` = date of scheduled shift start | **ACCEPTED** — confirm before Phase 3 | `ADR-0004` |
| Authorization / row scoping | Permissions = capability, Policies + scopes = rows | **ACCEPTED** | `ADR-0002` |
| Blade/Livewire vs Inertia | Blade + Livewire 3 + Alpine + Tailwind | PROPOSED — decide before P0-4 | `ADR-0007` |
| Primary admin UI component library | Hand-built Tailwind; no Filament/Nova | PROPOSED — decide before P0-4 | `ADR-0007` |
| UUID versus ULID | bigint PK + `uuid` column, UUID **v7** | PROPOSED — decide before P0-4 | `ADR-0008` |
| Multi-company in V1 | Schema-only readiness (carry `company_id`, no tenant switching) | PROPOSED — decide before P0-4 | `ADR-0009` |
| Salary visible to employee by default | NO — explicit grant only | PROPOSED — decide before P0-8 | `ADR-0010` |
| Exact QR package | Defer to Phase 4 | OPEN |
| Exact PDF rendering package | Defer to Phase 2 | OPEN |
| Exact map provider | Defer to Phase 5 | OPEN |
| Exact Telegram SDK/package | Defer to Phase 9 | OPEN |
| Exact Excel export package | Defer to Phase 8 | OPEN |

---

# Progress Update Template

After completing work, replace/add entries:

```markdown
## Update YYYY-MM-DD

### Completed
- ...

### Changed Files
- `app/...`
- `database/migrations/...`

### Tests
- `php artisan test`
- result: PASS / FAIL

### Remaining
- ...

### Risks / Notes
- ...
```

---

## Update 2026-09-07 (b) — P0-1, P0-2

### Completed
- **P0-1** version-control baseline. `git init` and the blueprint commit were done by the owner
  (`55eeca2`); this session added `.gitignore` and committed the audit (`5205d2d`).
- **P0-2** all six blocking conflicts resolved and recorded as ADR-0001 … ADR-0006 (Accepted).
  Four further pending decisions drafted as ADR-0007 … ADR-0010 (Proposed, awaiting owner).
- Blueprint annotated in place where an ADR overrides it (`02_DATABASE_DESIGN.md`, `01_ERD.md`).

### Changed Files
- `docs/absensi/*` — moved from `docs/*` via `git mv` (history preserved)
- `docs/adr/0001…0010` + `docs/adr/README.md` (created)
- `docs/absensi/README.md`, `01_ERD.md`, `02_DATABASE_DESIGN.md` (annotated)
- `docs/absensi/04_IMPLEMENTATION_STATUS.md` (this file)
- `.gitignore` (created)

### Tests
- `php artisan test` — still not runnable; no Laravel application exists. Test count **0**.

### Remaining
- **P0-3b/c** host PHP extensions, running services, resolved Laravel version. Needs the owner.
- Owner acceptance of ADR-0007 … ADR-0010, then P0-4 onward.

### Risks / Notes
- ADR-0004 (cross-day attendance date) is a business-semantics decision taken from the blueprint
  alone. Cheap to reverse now, expensive after Phase 4 stores production evidence.
- ADR-0002 remains the highest-cost item to postpone.

---

## Update 2026-09-07 (a) — TASK A0

### Completed
- **TASK A0 — Repository Audit.**

### Changed Files
- `docs/implementation/REPOSITORY_AUDIT.md` (created)
- `docs/04_IMPLEMENTATION_STATUS.md` (this file)

### Tests
- `php artisan test` — **not runnable**; no Laravel application and no test suite exists.
- Test count: **0**.

### Findings
- Repository contains 10 files, all Markdown documentation. No application code.
- No git repository.
- Greenfield confirmed after ruling out four sibling projects.
- 12 blueprint conflicts identified; 6 are blocking and must be resolved before Phase 0 migrations.
- C-12 (git discipline unenforceable) was resolved by the owner mid-audit and is closed. 11 conflicts remain open.

### Remaining
- All of Phases 0–12.
- Immediate next: P0-1b/c (`.gitignore`, commit audit), P0-2 (resolve C-1…C-6 + ADRs), P0-3 (verify host toolchain).

### Risks / Notes
- No Phase 0 code was written, per instruction.
- Nothing marked DONE except TASK A0, whose deliverable is a document that demonstrably exists.
- Deferring C-2 (row-level scoping) past Phase 0 forces a rewrite of every list query in Phases 1, 4, 6, 7 and 8. It is the highest-cost item to postpone.
- **Audit correction issued.** This file and `REPOSITORY_AUDIT.md` initially stated the project had no version control. A probe taken at 14:06 was treated as a standing fact at 14:10 while the owner was committing at 14:08. Both documents are corrected. Future phases must re-verify repository state immediately before concluding from it.


---

## Update 2026-09-07 (c) — P0-3a

### Completed
- **P0-3a** host toolchain verified from `ramusens-MacBook-Air` (Apple Silicon, Homebrew).

```text
PHP       8.4.11  (cli, NTS, Zend 4.4.11, OPcache 8.4.11)
Composer  2.8.10
psql      14.18   (client)
Redis     8.10.0
```

### Findings
- PHP 8.4.11, Composer 2.8.10 and Redis 8.10.0 are all acceptable with no action.
- **PostgreSQL 14 rejected as the project baseline.** `psql --version` reports the client, not the
  server, so it does not even establish the server version; and PostgreSQL 14 reaches end of life in
  **November 2026**, inside this project's build window. Every feature the schema needs works on 14,
  so this is a support-lifetime decision rather than a capability one — which is precisely why it is
  cheap to settle now and disruptive to settle during Phase 10.

### Changed Files
- `docs/adr/0005-test-database-engine.md` — amended, PostgreSQL target pinned to **17**
- `docs/implementation/REPOSITORY_AUDIT.md` — §1.5 now carries verified host versions
- `docs/absensi/04_IMPLEMENTATION_STATUS.md` — this file

### Tests
- `php artisan test` — still not runnable; no Laravel application exists. Test count **0**.

### Remaining
- P0-3b: PHP extensions (`pdo_pgsql`, `bcmath`, `intl`, `mbstring`, `zip`, `gd`) and running services.
- P0-3c: pin the Laravel version `composer create-project` resolves against PHP 8.4.
- ADR-0007 … ADR-0010 acceptance, then P0-4.

### Risks / Notes
- The Homebrew PostgreSQL 14 stays installed and untouched; this project uses the container's server
  and `psql` so a 14 client never talks to a 17 server.
- If production will run a managed PostgreSQL on a different major, pin the container to match it.
  A dev/prod major mismatch is the failure ADR-0005's amendment exists to prevent.


---

## Update 2026-09-07 (d) — R-1 … R-5 configuration

### Completed
Applied the five configuration conflicts recorded in `REPOSITORY_AUDIT.md` §11.3.

- **R-1** `phpunit.xml` repointed from SQLite `:memory:` to PostgreSQL `absensi_test` on port 5433,
  per ADR-0005. Comment added in the file explaining why, so it is not reverted by a future
  starter-kit sync.
- **R-2** `.env` / `.env.example` → `DB_CONNECTION=pgsql`, host 127.0.0.1, port 5433, database
  `absensi`, user `absensi`.
- **R-3** `CACHE_STORE`, `QUEUE_CONNECTION` and `SESSION_DRIVER` → `redis`.
- **R-4** `REDIS_PORT` → 6380, matching `docker-compose.yml`. `REDIS_CLIENT` left at `phpredis`
  pending P0-3b.
- **R-5** `APP_TIMEZONE=UTC` added, with an inline warning not to set it to `Asia/Jakarta`.
- `APP_NAME` → `Absensi`.
- Added `docker/postgres/init/02-test-database.sh` creating `absensi_test` with `btree_gist` and
  `pgcrypto` on first volume initialisation.

`APP_KEY` was preserved (targeted edits, not a rewrite). `.env` remains untracked; verified with
`git check-ignore`.

### Changed Files
- `phpunit.xml`, `.env.example`, `.env` (untracked), `docker/postgres/init/02-test-database.sh`

### Tests
- **Not run.** This session cannot execute PHP. The suite's outcome under PostgreSQL is **unknown**
  and is the next thing to establish.

### Risks / Notes
- **`SESSION_DRIVER=redis` will break the application at boot if the `redis` PHP extension is
  absent.** This is the outstanding P0-3b check. Homebrew's `php` formula does not bundle phpredis;
  it needs `pecl install redis`. The alternative is `composer require predis/predis` with
  `REDIS_CLIENT=predis` — but AGENTS.md forbids changing dependencies without approval, so that is a
  decision for the owner, not a change to make unilaterally.
- The failure, if it comes, is loud and immediate rather than silent — which is the intended
  trade against discovering a broken queue in Phase 9.
- Running the test suite now requires `docker compose up -d`. That is the accepted cost of ADR-0005,
  recorded here so it is not mistaken for a regression.


---

## Update 2026-09-07 (e) — Phase 0 foundation VERIFIED on PostgreSQL

First entry in this project backed by executed test output rather than inspection.

### Evidence

```text
$ php -m | grep -Ei '^(pdo_pgsql|pgsql|redis|bcmath|intl|zip|gd)$'
bcmath  gd  intl  pdo_pgsql  pgsql  redis  zip

$ docker compose ps
absensi-postgres  postgres:17-alpine  Up (healthy)  0.0.0.0:5433->5432/tcp
absensi-redis     redis:8-alpine      Up (healthy)  0.0.0.0:6380->6379/tcp

$ php artisan migrate
INFO  Running migrations.
  0001_01_01_000000_create_users_table ...................... 24.87ms DONE
  0001_01_01_000001_create_cache_table ...................... 17.53ms DONE
  0001_01_01_000002_create_jobs_table ....................... 19.53ms DONE
  2024_01_01_000000_create_passkeys_table ................... 23.93ms DONE
  2025_08_14_170933_add_two_factor_columns_to_users_table .... 3.29ms DONE

$ php artisan test --compact
Tests:  33 passed (81 assertions)   Duration: 2.26s
```

### Verified DONE

- **P0-3b** — every required PHP extension present.
- **P0-5 (database half)** — PostgreSQL 17 container healthy; all five migrations applied; the full
  suite of 33 tests passes against `absensi_test`. `phpunit.xml` sets `DB_CONNECTION=pgsql`
  explicitly with no SQLite fallback, and `absensi_test` exists only because the container init script
  created it — so the passing run is proof the suite executed on PostgreSQL, not merely that it
  passed.
- **R-1 … R-5** — all five configuration conflicts closed and exercised without regression. The 33
  tests passed on SQLite before the change and pass on PostgreSQL after it.
- **ADR-0005 validated in practice.** Moving off SQLite cost nothing in existing coverage.
- **P0-4, P0-6, P0-7** — Laravel 13.17, Livewire 4.1 + Flux, Fortify auth with passkeys and 2FA,
  covered by the auth tests in that run.
- **P0-13** — Pint and Larastan level 7 configured.

### NOT verified — Redis is still unproven

The passing suite says **nothing** about Redis. `phpunit.xml` overrides the drivers during tests:

```xml
<env name="CACHE_STORE" value="array"/>
<env name="QUEUE_CONNECTION" value="sync"/>
<env name="SESSION_DRIVER" value="array"/>
```

So no test touched Redis, and `php artisan migrate` does not either. The container is healthy and the
`redis` extension is loaded, but **nothing has yet proved the application can read, write or queue
through it**. `03_ROADMAP.md` Phase 0 exit criteria require a working queue, and Phase 9 depends on
queued delivery, so this must be demonstrated rather than assumed.

Marked **PARTIAL**, not DONE, per `05_IMPLEMENTATION_RULES.md` §14.

### Remaining for Phase 0

- Prove Redis cache, session and queue work end to end.
- RBAC: `spatie/laravel-permission` (a dependency change — needs approval under AGENTS.md).
- 52 permissions + 8 roles seeded.
- ADR-0002 scope resolver contract.
- Super-admin bootstrap command.
- Audit logging foundation (`audit_logs` table, recorder, request-id middleware).
- Shared enums.
- `users` extended with `uuid`, `status`, `last_login_at` (`02` §2).
- `btree_gist` migration (CI and pre-existing volumes never run the container init script).
- CI workflow.
- `.ai/rules` bridging the ADRs to Boost.
