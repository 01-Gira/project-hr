# Absensi — Repository Audit (TASK A0)

**Audit date:** 2026-09-07
**Audited path:** `/Users/ramusen/Projects/project-hr`
**Auditor method:** direct filesystem inspection. No assumptions carried over from the blueprint.
**Scope:** TASK A0 only. No Phase 0 implementation performed. No code written.

> **Correction applied 2026-09-07 (post-publication).** The first issue of this audit stated that the
> repository was not under version control. That was true when probed at 14:06 local time and **false by
> 14:08**, when the repository owner ran `git init` and committed the ten blueprint documents
> (`55eeca2`, author `Gira <giramnk@gmail.com>`) while this audit was being written. The finding was a
> stale point-in-time probe, not a standing fact. Sections 1.2, 2, 6 (C-12), 7 (P0-1), 8 (TD-1) and 10
> are corrected below. **The greenfield verdict in §0 is unaffected** — the commit contains documentation
> only, no application code.

---

## 0. Verdict

> ### SUPERSEDED 2026-09-07 by §11
>
> The greenfield verdict below was accurate when written. It is **no longer true**: the owner installed
> the **Laravel Livewire starter kit** (Laravel 13.17, PHP ^8.3) in commit `408eb13` while this document
> was being extended. §1 and §2 are preserved as the historical record of the pre-install state; **§11
> is the current audit.** Read §11 first.

> **There is no Laravel application in this repository.** *(true as of 14:06–14:20, superseded)*
> The repository contains blueprint documentation only. Phase 0 is a **greenfield build**, not a reconciliation.

Every stack question posed by TASK A0 (Laravel version, PHP version, frontend stack, database driver, Redis, auth, RBAC, migrations, models, modules, tests, lint tooling) resolves to **NOT PRESENT**, evidenced below.

---

## 1. Evidence — What Actually Exists

### 1.1 Complete file inventory

Command run in the repository root:

```bash
find . -type f
```

Result — **10 files, 100% Markdown, all under `docs/`:**

```text
./docs/README.md
./docs/00_PROJECT_CONTEXT.md
./docs/01_ERD.md
./docs/02_DATABASE_DESIGN.md
./docs/03_ROADMAP.md
./docs/04_IMPLEMENTATION_STATUS.md
./docs/05_IMPLEMENTATION_RULES.md
./docs/06_CODEX_TASKS.md
./docs/07_PERMISSION_MATRIX.md
./docs/08_UI_UX_BLUEPRINT.md
```

```bash
find . -type f | wc -l
# 10
```

Directory listing of the root confirms a single child directory:

```bash
ls -la
# drwx------  docs
```

There are no dotfiles, no hidden directories, and no files at any depth other than the ten above.

### 1.2 Version control

**Corrected finding: the repository IS under version control.**

Timeline, from `.git` metadata and reflog:

| Local time | Event |
|---|---|
| 14:06 | Audit probe: `git rev-parse --show-toplevel` -> `fatal: not a git repository`. Accurate at that moment. |
| 14:07 | `.git` created (HEAD, description, hooks, objects all timestamped 14:07). |
| 14:08 | Initial commit `55eeca2` — *"Add comprehensive documentation for Absensi project"*, author and committer `Gira <giramnk@gmail.com>`. |
| 14:10 | This audit written, still carrying the stale 14:06 finding. |
| 14:13 | Re-probe on resuming work; discrepancy detected and corrected. |

Current verified state:

```bash
git rev-parse --show-toplevel   # /Users/ramusen/Projects/project-hr
git log --oneline               # 55eeca2 Add comprehensive documentation for Absensi project
git branch                      # * main
git ls-files                    # 10 files, all under docs/
```

Tracked: the ten blueprint Markdown documents. Branch: `main`. One commit. Reflog: one entry.

**Remaining version-control gaps** (real, and still P0):

- **No `.gitignore`.** Committing a Laravel install without one will track `vendor/`, `node_modules/`, `.env` and `public/build`. `.env` in particular is a credential leak. Must exist *before* P0-4.
- `docs/implementation/` (this audit) is untracked.
- `docs/absensi/04_IMPLEMENTATION_STATUS.md` has uncommitted modifications.

`05_IMPLEMENTATION_RULES.md` §15 (Git Discipline) is now **enforceable** — `git status`, `git diff` and `git diff --check` all work.

**Process note for later phases:** this repository is being modified concurrently by its owner. Re-run `git status` and `git log` immediately before writing any conclusion about repository state, and again before committing. A probe result older than a few minutes is not evidence.

### 1.3 Files that would prove a Laravel project — all absent

| Expected artifact | Present? | Consequence |
|---|:--:|---|
| `composer.json` | **NO** | Laravel version, PHP constraint, and all dependencies are undetermined |
| `composer.lock` | **NO** | No resolved dependency graph |
| `artisan` | **NO** | No Laravel console; `php artisan test` cannot run |
| `app/` | **NO** | No models, no domain layer, no policies |
| `bootstrap/app.php` | **NO** | No application bootstrap; Laravel 11/12 style config undetermined |
| `config/` | **NO** | No database, cache, queue, or session config |
| `database/migrations/` | **NO** | Zero migrations exist |
| `routes/` | **NO** | No routes, web or API |
| `resources/views/` | **NO** | No Blade templates |
| `public/index.php` | **NO** | No web entrypoint |
| `vendor/` | **NO** | No installed dependencies |
| `.env` / `.env.example` | **NO** | No environment contract |
| `package.json` | **NO** | Frontend stack undetermined |
| `vite.config.js` | **NO** | No asset pipeline |
| `tailwind.config.js` | **NO** | Tailwind not configured |
| `phpunit.xml` | **NO** | No test harness |
| `tests/` | **NO** | Zero tests |
| `pint.json` / `.php-cs-fixer.php` | **NO** | No formatter configured |
| `phpstan.neon` / `psalm.xml` | **NO** | No static analysis configured |
| `.github/workflows/` | **NO** | No CI |
| `docker-compose.yml` / `Dockerfile` | **NO** | No local service definitions for PostgreSQL/Redis |

### 1.4 Ruling out the code living elsewhere

Because `05_IMPLEMENTATION_RULES.md` §1 states *"Do not assume a blank project if code already exists"*, sibling directories under `/Users/ramusen/Projects/` were inspected before concluding greenfield:

| Candidate | Observed structure | Is it Absensi/Laravel? |
|---|---|---|
| `absence-app` | `venv`, `__pycache__`, `dataset`, `descriptors`, `models`, `migrations`, `nginx`, `ssl` | **No** — Python application (structure indicates a face-recognition/biometric attendance system). Not Laravel. |
| `ramusen-crm` | `app`, `bootstrap`, `config`, `database`, `public`, `resources`, `routes`, `storage`, `tests`, `vendor` | **Laravel, but a different product (CRM).** Not Absensi. Must not be reused as the Absensi root. |
| `ramusen` | `app`, `bootstrap`, `resources`, `routes`, `vendor`, plus `cgi-bin`, `PHPMailer`, `captcha`, `uploads` | **No** — legacy mixed PHP application. Not the Absensi target. |
| `bantuwarga` | `.next`, `prisma`, `src`, `.pnpm-store` | **No** — Next.js / Prisma. |

Remaining siblings (`floonk_backend`, `plonk_application`, `ploonk-cp`, `ploonk-cp-backend`, `ploonk-react`, `ramusen-autodialer`, `ramusen-socket`, `ramusenEA`, `ramusenEA-backup-before-filter`, `side-projects`, `test`) are named for unrelated products and none is a plausible Absensi codebase.

**Conclusion: no prior Absensi Laravel implementation exists anywhere reachable. Greenfield is confirmed, not assumed.**

### 1.5 Toolchain observed from the audit shell

```bash
php       NOT FOUND
composer  NOT FOUND
psql      NOT FOUND
redis-cli NOT FOUND
node      v22.23.2
npm       10.9.8
```

**Caveat — do not over-read this.** These probes ran in the session's Linux workspace, not on the macOS host where development will happen. This is evidence that the *audit environment* has no PHP toolchain; it is **not** evidence about the developer machine.

### Host toolchain — VERIFIED 2026-09-07 (P0-3)

Reported by the owner from `ramusens-MacBook-Air`, Apple Silicon, Homebrew:

| Tool | Version | Assessment |
|---|---|---|
| PHP | **8.4.11** (cli, NTS, Zend 4.4.11, OPcache 8.4.11) | **OK.** Current Laravel supports 8.3–8.4. |
| Composer | **2.8.10** | **OK.** |
| psql | **14.18** | **CLIENT ONLY, and the wrong major — see below.** |
| Redis | **8.10.0** | **OK.** |

**PostgreSQL 14 is not an acceptable base for this project.**

1. `psql --version` reports the **client**. It does not establish that a server is running, nor the
   server's version. Those are different things and only the server version constrains the schema.
2. PostgreSQL 14 reaches **end of life in November 2026** — roughly fourteen months from now. This is
   a greenfield HR and payroll system whose Phase 10 handles money. Starting it on a major version
   that leaves security support inside its first year of development is a straightforward mistake:
   the upgrade lands mid-build, during the payroll phases, rather than before Phase 1.
3. Everything the schema needs (`btree_gist`, `EXCLUDE USING gist`, `daterange`, `jsonb`, `inet`)
   works on 14, so this is **not** a capability problem. It is purely a support-lifetime problem —
   which is why it is cheap to fix now and annoying to fix later.

**Resolution:** `ADR-0005` is amended. The project targets **PostgreSQL 17** supplied by
`docker-compose.yml`, identical in local development, test and CI. The Homebrew PostgreSQL 14
installation is left untouched and unused by this project; use the container's own `psql`.

**Still outstanding in P0-3:** the required PHP extensions and running services have not been
confirmed. See the status document.

---

## 2. Required Audit Findings — Point by Point

| # | TASK A0 requirement | Finding | Evidence |
|---|---|---|---|
| 1 | Laravel version | **NOT PRESENT** | no `composer.json`, no `artisan` |
| 2 | PHP version requirement | **NOT PRESENT** | no `composer.json` `require.php` constraint |
| 3 | Frontend stack | **NOT PRESENT** | no `package.json`, no `vite.config.js`, no `resources/` |
| 4 | Database driver | **NOT PRESENT** (blueprint specifies PostgreSQL) | no `config/database.php`, no `.env` |
| 5 | Redis / queue | **NOT PRESENT** | no `config/queue.php`, no `config/cache.php`, no compose file |
| 6 | Authentication | **NOT PRESENT** | no `app/Models/User.php`, no auth scaffolding, no `routes/auth.php` |
| 7 | Role / permission implementation | **NOT PRESENT** | no `spatie/laravel-permission` in a lockfile that does not exist; no `config/permission.php` |
| 8 | Current migrations | **ZERO** | `database/migrations/` does not exist |
| 9 | Current models | **ZERO** | `app/Models/` does not exist |
| 10 | Current modules | **ZERO** | no `app/`, no `app/Domain/` |
| 11 | Current tests | **ZERO** | no `tests/`, no `phpunit.xml`; test count = 0 |
| 12 | Formatter / static analysis | **NOT PRESENT** | no Pint, PHP-CS-Fixer, PHPStan, Larastan, or Psalm config |
| 13 | What is already implemented | **NOTHING.** Documentation only. | §1.1 |
| 14 | What is missing | **Everything.** Phases 0–12 in full. | §5 |
| 15 | Conflicts with the blueprint | **12 identified; C-12 has since been resolved by the owner. 11 open.** | §6 |
| 16 | Exact recommended Phase 0 work | **P0-1 … P0-14.** | §7 |

---

## 3. Documentation Audit

The ten blueprint documents are internally coherent, detailed, and usable as a build specification. They are the only asset this repository currently holds.

| Document | Bytes | Assessment |
|---|---:|---|
| `README.md` | 421 | Index + reading order. **Path claim is wrong — see C-1.** |
| `00_PROJECT_CONTEXT.md` | 4,972 | Strong. Architectural invariants are explicit and correct (QR ≠ attendance; events immutable; effective-dated salary). |
| `01_ERD.md` | 4,431 | Four Mermaid ERDs. Consistent with `02` apart from C-3. |
| `02_DATABASE_DESIGN.md` | 13,056 | The most load-bearing document. 30 tables, constraints, indexes. Reviewed in §6. |
| `03_ROADMAP.md` | 8,306 | 13 phases with exit criteria and a Definition of Done. Sound sequencing. |
| `04_IMPLEMENTATION_STATUS.md` | 3,945 | Accurate — already states 0% / NOT IMPLEMENTED. Updated by this audit. |
| `05_IMPLEMENTATION_RULES.md` | 5,089 | Enforceable rules. §15 currently unenforceable (no git). |
| `06_CODEX_TASKS.md` | 4,230 | Task queue A0–A11. This document satisfies A0. |
| `07_PERMISSION_MATRIX.md` | 1,819 | Product-level matrix. **Not directly implementable — see C-2.** |
| `08_UI_UX_BLUEPRINT.md` | 5,244 | Screen-level guidance, navigation trees, responsive targets. Adequate for Phase 0 shell. |

---

## 4. What Is Already Implemented

```text
NOTHING.
```

Per `05_IMPLEMENTATION_RULES.md` §14 (No Fake Completion), nothing may be marked DONE. There is no code, therefore no feature works, no authorization works, no validation works, and no test passes. Implemented database tables: **0 of ~30**. Implemented modules: **0 of 17 domains**.

---

## 5. What Is Missing

Everything in `03_ROADMAP.md`. Restated at the level that matters for planning:

**Phase 0 (Foundation) — 0% .** No Laravel install, no PostgreSQL, no Redis, no queue, no auth, no RBAC, no seeders, no audit foundation, no enums, no exception conventions, no test infra, no CI.

**Phases 1–12 — 0%.** All 30 tables from `02_DATABASE_DESIGN.md` are unimplemented, including every table on the critical attendance path: `attendance_policies`, `qr_sessions`, `attendance_events`, `attendances`, `attendance_correction_requests`, `locations`, `geofences`, `employee_schedules`, `shifts`.

**Prerequisites missing that are not in the roadmap at all:**
- Git repository initialisation (blocks rule §15).
- Local service orchestration for PostgreSQL + Redis (blocks Phase 0 exit criteria).
- A decision on the test database engine (blocks all testing — see C-5).

---

## 6. Conflicts and Gaps Versus the Blueprint

Twelve issues found by reading `01`, `02`, `05`, `07` against each other. Six of them (C-1 … C-6) must be resolved **before** Phase 0 code is written, because each one changes migration or architecture output.

### C-1 — Documentation path does not match reality  *(severity: low, fix now)*
`README.md` states the recommended location is `docs/absensi/`. The files are actually at `docs/`. Every prompt in `06_CODEX_TASKS.md` ("Read all Absensi blueprint files in `docs/absensi/`") therefore points at a non-existent path.
**Fix:** either move the ten files to `docs/absensi/` or correct `README.md` and `06_CODEX_TASKS.md`. Moving is preferable — it namespaces Absensi docs against future `docs/implementation/`, `docs/adr/` siblings, and this audit already assumes `docs/implementation/` exists as a peer.

### C-2 — The permission matrix is not expressible in Spatie permissions alone  *(severity: HIGH — architectural)*
`07_PERMISSION_MATRIX.md` uses cell values `team`, `restricted`, `optional`, `approve-team`, `request-own`, `lookup`, `own`, `read`. `spatie/laravel-permission` grants a flat, global permission — it has **no concept of row scope**. `$user->can('attendance.view-team')` cannot by itself answer *which* team.
This is the single largest gap between blueprint and implementable design. `00_PROJECT_CONTEXT.md` §3.6 correctly insists on permissions over role names, but permissions are only half the model.
**Required design decision before Phase 0:** permissions answer *capability*; **Laravel Policies + Eloquent query scopes** must answer *scope*. Concretely, `attendance.view-team` must be paired with a `scopeVisibleTo(User)` on the `Attendance` model that filters by `employee_profiles.manager_id` chain / `branch_id` / `department_id`. Design the scope resolver in Phase 0 as part of the RBAC foundation, even though the tables it filters arrive in Phases 1–4. Deferring this forces a rewrite of every list query in Phases 1, 4, 6, 7 and 8.

### C-3 — Circular foreign keys between `attendances` and `attendance_events`  *(severity: HIGH — blocks migration order)*
`02_DATABASE_DESIGN.md` §10 defines:
- `attendances.check_in_event_id` and `attendances.check_out_event_id` → reference `attendance_events`
- `attendance_events.attendance_id` (nullable) → references `attendances`

These tables reference each other. A naive pair of migrations with both FK constraints declared inline will fail on the first `php artisan migrate`.
**Fix:** create both tables without the cross constraints, then add the FKs in a third migration; make `attendance_events.attendance_id` nullable (already specified) and populate `attendances.check_in_event_id` by `UPDATE` after event insert inside the existing scan transaction. Record this in an ADR so Phase 4 does not rediscover it.

### C-4 — `UNIQUE(employee_id, attendance_date)` contradicts cross-day shifts  *(severity: HIGH — correctness)*
`02` §10 mandates `UNIQUE(employee_id, attendance_date)`. `02` §6 defines `shifts.cross_day boolean`. `03_ROADMAP.md` Phase 3 requires cross-day shift support and `05` §12 requires a cross-day shift test.
A night shift starting 22:00 Monday and ending 06:00 Tuesday produces check-in and check-out on different calendar dates. The blueprint never states which date owns the record.
`02` §10 offers the escape hatch — `UNIQUE(employee_id, employee_schedule_id)` — but does not choose.
**Recommendation, plainly: adopt `attendance_date = the date of the shift's scheduled start`, keep `UNIQUE(employee_id, attendance_date)` for V1, and write the rule into `02` as a frozen decision.** It is simpler, keeps the daily-report queries in Phase 8 trivial, and matches how attendance is spoken about operationally ("Monday's night shift"). Switch to `UNIQUE(employee_id, employee_schedule_id)` only if and when multiple independent shifts per day become a real requirement. Do not leave this undecided into Phase 4.

### C-5 — PostgreSQL-specific column types break SQLite testing  *(severity: HIGH — blocks test infra)*
`02` specifies `inet` for `attendance_events.ip_address`, `qr_sessions.generated_ip`, and `audit_logs.ip_address`. Laravel's schema builder has **no `inet` type**; it requires `DB::statement` raw DDL or `$table->addColumn('inet', ...)`. SQLite has no `inet` type at all, and `numeric(10,7)` / `jsonb` behave differently there too.
The common Laravel default of `:memory:` SQLite for tests will therefore either fail outright or silently test different semantics than production — which is worse.
**Decision required in Phase 0: run the test suite against a real PostgreSQL database**, not SQLite. This is a Phase 0 deliverable (P0-3, P0-11), not a Phase 4 problem. Alternatively drop `inet` in favour of `varchar(45)` (holds IPv6) and lose PostgreSQL's native validation — acceptable, and it restores SQLite portability. **Recommendation: keep PostgreSQL for tests and keep `inet`**; the geofence and JSONB work in Phases 5 and 4 will need real PostgreSQL semantics anyway, so SQLite parity is a false economy.

### C-6 — Table naming inconsistency: `employees` vs `employee_profiles`  *(severity: medium)*
`02` §4 defines the table as **`employee_profiles`**. `02` §18 (Critical Database Constraints) then writes the constraint as **`employees(company_id, employee_number) UNIQUE`**. `01_ERD.md` consistently uses `EMPLOYEE_PROFILES`.
**Fix:** `employee_profiles` is correct and better — it makes the `users` / workforce-identity split explicit, which `01` §5 rules 1–3 depend on. Correct §18 to say `employee_profiles`.

### C-7 — Foreign key column naming is inconsistent across the design  *(severity: medium)*
`02` uses `employee_id` throughout (`attendances.employee_id`, `qr_sessions.employee_id`, `employee_devices.employee_id`) while the referenced table is `employee_profiles`. Laravel convention would infer `employee_profile_id`.
**Fix:** keep `employee_id` (shorter, and the domain word is "employee"), but declare the relationship explicitly on every model — `belongsTo(EmployeeProfile::class, 'employee_id')` — and state the convention once in an ADR. The risk is silent breakage from Eloquent's default inference, not the name itself.

### C-8 — `holidays` branch scope missing from the ERD  *(severity: low)*
`02` §6 gives `holidays.branch_id nullable`. `01` §1 shows only `HOLIDAYS }o--|| COMPANIES : belongs`. The branch relation is absent from the ERD.
**Fix:** add `BRANCHES ||--o{ HOLIDAYS : scopes` to `01`.

### C-9 — `qr_sessions` has no `updated_at`  *(severity: low)*
`02` §8 lists `created_at` but no `updated_at`, while the row is mutated after creation (`used_at`, `revoked_at`).
**Fix:** either add `updated_at` or set `public $timestamps = false;` with explicit `const CREATED_AT`. Leaving it implicit will throw on first `->save()`. Same applies to `attendance_events` (`created_at` only — correct there, since it is append-only, but must be declared).

### C-10 — `settings` table has redundant scoping  *(severity: low)*
`02` §16 defines both `company_id nullable` **and** `scope_type` / `scope_id`. A company-scoped setting is representable two ways, which guarantees inconsistent reads.
**Fix:** drop `company_id` and express company scope as `scope_type='company'`, or drop the polymorphic pair. **Recommendation: keep the polymorphic pair only**, and add `UNIQUE(scope_type, scope_id, key)`.

### C-11 — No unique constraint specified on `employee_schedules` work-mode/shift pairing, and `leave_requests` has no overlap guard  *(severity: medium)*
`02` §18 lists `employee_schedules(employee_id, schedule_date) UNIQUE` — good. But `employee_salaries` non-overlap (mandated as a hard rule by `00` §3.4, `02` §13 and `05` §7) has **no DB-level enforcement specified**, and `leave_requests` has no overlap rule at all. Application-level checks alone lose to concurrency, and `05` §5 explicitly demands DB-level protection for concurrency-sensitive paths.
**Fix:** PostgreSQL `EXCLUDE USING gist` with `btree_gist` on `employee_salaries (employee_id WITH =, daterange(effective_from, effective_until) WITH &&)` gives real enforcement. Same technique applies to `leave_requests`. This requires the `btree_gist` extension — enable it in the Phase 0 migration so Phases 6 and 7 can use it.

### C-12 — Rule §15 (Git Discipline) was unenforceable  *(severity: HIGH — process)* — **RESOLVED 2026-09-07**
`05` §15 mandates `git status` / `git diff` / `git diff --check`. At the time of the initial probe there was no git repository, so every later phase would have been required to run commands that fail, with no rollback path.
**Resolved by the repository owner** at 14:08 with commit `55eeca2` (§1.2). Rule §15 is now enforceable.
**Residual work, still P0:** no `.gitignore` exists. Creating one is a hard prerequisite for P0-4 (Laravel install), otherwise `vendor/`, `node_modules/` and `.env` get committed.

### Pending decisions from `04` — recommendations, stated plainly

`04_IMPLEMENTATION_STATUS.md` lists ten open decisions. Six of them should be closed before Phase 0, because Phase 0 output depends on them:

| Decision | Recommendation | Reason |
|---|---|---|
| Blade/Livewire vs Inertia | **Blade + Livewire 3 + Alpine + Tailwind** | Already the stated recommendation in `00` §1. Livewire's server-driven model suits the QR countdown, scanner result panel and live attendance board (`08` §6–8) with far less machinery than an Inertia+Vue/React SPA. One language, one auth model, no API surface needed until Phase 12. |
| Admin UI component library | **Tailwind + hand-built components; no admin framework (not Filament/Nova)** | Filament would deliver Phases 1–3 faster but its resource abstraction actively fights the custom flows in Phases 4, 5 and 9 (scanner, geofence map, QR lifecycle), and `05` §3 asks for domain Actions rather than framework-coupled CRUD. Locking the sensitive salary and attendance UI into a third-party admin panel is the wrong trade for this system. |
| UUID vs ULID | **bigint PK + `uuid` column (UUID v7)** | `02` §1 already recommends it. UUID v7 is time-sortable, so it keeps the index-locality benefit that motivates ULID while staying a native PostgreSQL `uuid` type. Do **not** use UUID v4 as PK. |
| Multi-company in V1 | **Schema-only readiness** | `company_id` is already on every relevant table in `02`. Carry the column and the FK; do not build tenant switching, tenant-scoped middleware or per-tenant config in V1. Cost now is one column; cost of retrofitting later is a full migration of every table. |
| Salary visible to employee by default | **NO** | `00` §6 and `05` §7 both separate salary permission from profile permission. Default `salary.view-own` to the employee role **off**; make it an explicit grant. |
| Test database engine | **PostgreSQL** (see C-5) | Blocks Phase 0 test infrastructure. |

The remaining four (QR package, PDF package, map provider, Telegram SDK, Excel export) correctly belong at their respective phases and should be chosen against the then-current Laravel/PHP compatibility, exactly as `04` states. Do not pin them now.

---

## 7. Exact Recommended Phase 0 Scope

Ordered. Each item is sequenced so attribution is never lost — **one change at a time, verified before the next**, per `05` §2 and §15.

### P0-1 — Complete the version-control baseline  *(partially done by owner)*
- ~~`git init`~~ — **DONE** by the repository owner, commit `55eeca2`, 2026-09-07 (§1.2).
- ~~Commit the ten blueprint docs as the baseline~~ — **DONE** in that same commit.
- **Still required:** create `.gitignore` with the Laravel defaults — `/vendor`, `/node_modules`, `/.env`, `/.env.backup`, `/public/build`, `/public/hot`, `/storage/*.key`, `.phpunit.result.cache`, `/.idea`, `/.vscode`, `.DS_Store`. This is a hard prerequisite for P0-4; without it the Laravel install commits `vendor/` and `.env`.
- **Still required:** commit this audit and the updated status document, so the pre-implementation state is a named commit that later phases can diff against.
- **Exit:** `.gitignore` exists and is committed; `git status` clean; rule §15 enforceable *(already true)*.

### P0-2 — Resolve C-1 through C-6 in the documents  *(no code)*
Update `README.md`, `01_ERD.md`, `02_DATABASE_DESIGN.md` with the decisions above. Add `docs/adr/` and record: cross-day attendance date rule (C-4), test DB engine (C-5), scope-vs-permission model (C-2), circular FK strategy (C-3), FK naming (C-7). **Do this before writing migrations** — every one of these changes migration output.

### P0-3 — Verify and pin the host toolchain
- Confirm actual `php -v`, `composer -V`, PostgreSQL and Redis availability **on the macOS host** (unverified — see §1.5).
- Target **PHP 8.3+** and the current Laravel LTS/stable at install time. Record exact versions in this audit's §1.5 once known.
- **Exit:** versions written down; no guessing in later phases.

### P0-4 — Install Laravel and commit untouched
`composer create-project laravel/laravel .` (or into a subdirectory if the docs stay at repo root). Commit the pristine skeleton as its own commit so every later diff is attributable.

### P0-5 — Configure PostgreSQL, Redis, queue, timezone
- `.env.example` + `.env`: `DB_CONNECTION=pgsql`, `CACHE_STORE=redis`, `QUEUE_CONNECTION=redis`, `SESSION_DRIVER=redis`.
- `docker-compose.yml` for PostgreSQL 16 + Redis 7 so the environment is reproducible (currently nothing defines these services).
- Timezone strategy per `02` §1: `APP_TIMEZONE=UTC` for storage; display timezone resolved from company settings, default `Asia/Jakarta`. Do **not** set `APP_TIMEZONE=Asia/Jakarta` — that silently corrupts the UTC storage rule.
- Enable `btree_gist` in the first migration (needed by C-11 in Phases 6–7).
- **Exit:** `php artisan migrate` succeeds against PostgreSQL; `php artisan queue:work` connects to Redis.

### P0-6 — Frontend stack
Install Livewire 3, Alpine, Tailwind, Vite per the C-decision above. Build the app shell only: layout, sidebar skeleton from `08` §4, and permission-gated menu rendering. No feature screens.

### P0-7 — Authentication
Laravel Breeze (Blade+Livewire stack) or hand-rolled. Add `users.uuid`, `users.status`, `users.last_login_at` per `02` §2 — these are **not** in the default Laravel users migration and must be added now, not retrofitted.
- **Exit criterion from `03`:** login works.

### P0-8 — RBAC foundation
- `spatie/laravel-permission`, published config and migration.
- **Permission seeder** with the full list from `02` §2 verbatim (~60 permissions). Seed all of them now even though most guard features that do not exist yet — this is the contract Phases 1–12 code against.
- **Role seeder** for the 8 roles in `00` §5, mapped from `07_PERMISSION_MATRIX.md`, with `team`/`restricted`/`optional` cells resolved per the C-2 decision.
- **Scope resolver skeleton** (C-2): a `ScopesToUser` contract / trait defining `scopeVisibleTo(Builder, User)`, with no models implementing it yet. This is the piece that must not be deferred.
- **Exit criterion from `03`:** permission checks work.

### P0-9 — Super-admin bootstrap
Idempotent seeder or `php artisan absensi:create-super-admin` command. Must not hardcode a password; read from env or prompt. Document it in `04`.
- **Exit criterion from `03`:** super-admin seeded.

### P0-10 — Audit logging foundation
- `audit_logs` migration exactly per `02` §17 (including `inet` per C-5 and `request_id`).
- `AuditLog` model + a `RecordsAudit` service/action taking `actor, action, subject, old, new, reason, ip, user_agent, request_id`.
- Middleware assigning a per-request `request_id`.
- **Exit criterion from `03`:** audit can record a sample admin action — prove it with a test, not a manual click.

### P0-11 — Common enums and exception conventions
- `app/Domain/Shared/Enums/` with `Status`, and the enums already frozen in `02`: `WorkMode`, `AttendanceStatus`, `AttendanceEventType`, `AttendanceSource`, `QrPurpose`, `IdCardStatus`, `RequestStatus`, `PaymentFrequency`, `SalaryComponentType`, `SalaryComponentDirection`, `DeliveryMode`. Backed enums, string-valued, matching `02` verbatim.
- Domain exception base class + handler mapping to consistent JSON/Blade error responses. Phase 4's scanner needs the specific error strings in `08` §7 — define the exception-to-message contract now.

### P0-12 — Test infrastructure
- Pest or PHPUnit against **PostgreSQL** (C-5), with a dedicated test database and `RefreshDatabase`.
- Factories for `User` and `Role`.
- Write real Phase 0 tests, not placeholders: login succeeds; login fails on bad credentials; unauthenticated user is redirected from dashboard; a user lacking `dashboard.view` is 403'd; a permission check passes for a granted permission; the audit action writes a row with correct actor/subject.
- **Exit criterion from `03`:** tests green — and this is the only acceptable proof for marking Phase 0 DONE per `05` §14.

### P0-13 — Formatting and static analysis
- Laravel Pint (`pint.json`, Laravel preset).
- **Larastan level 5 minimum** to start; raise per phase. Level 5 catches the model-property and relationship errors that C-7's naming exception will otherwise hide.
- Document commands in `README.md`: `composer test`, `composer lint`, `composer analyse`.

### P0-14 — CI
GitHub Actions: PostgreSQL + Redis services, `composer install`, `php artisan test`, `pint --test`, `phpstan analyse`. Required before Phase 1 so no phase can regress silently.

### Phase 0 exit gate

Do not open Phase 1 until **all** of the following are demonstrably true:

```text
[ ] git repository exists, clean, with attributable per-step commits
[ ] C-1 … C-6 resolved in docs and recorded as ADRs
[ ] php artisan migrate succeeds on PostgreSQL
[ ] Redis-backed queue processes a test job
[ ] login works
[ ] protected dashboard rejects unauthenticated and unpermitted users
[ ] super-admin seeded via repeatable command
[ ] ~60 permissions and 8 roles seeded
[ ] scope resolver contract exists
[ ] audit log records a sample admin action, proven by test
[ ] php artisan test green
[ ] pint --test and phpstan clean
[ ] CI green on a pull request
[ ] 04_IMPLEMENTATION_STATUS.md updated with evidence
```

---

## 8. Technical Debt Register

Debt carried into the project on day zero:

| ID | Debt | Impact | Where addressed |
|---|---|---|---|
| TD-1 | ~~No version control~~ — **RESOLVED** by owner, commit `55eeca2`. Residual: no `.gitignore`, which will leak `.env` and `vendor/` at P0-4 | Credential leak + repo bloat on Laravel install | P0-1 |
| TD-2 | Docs at `docs/` not `docs/absensi/`; task prompts point at a dead path | Every Codex prompt in `06` misfires | P0-2 / C-1 |
| TD-3 | Row-level scoping model undesigned | Rewrite of all list queries in Phases 1,4,6,7,8 if deferred | P0-8 / C-2 |
| TD-4 | Cross-day attendance date rule undefined | Phase 3 and Phase 4 will disagree; corrupt data | P0-2 / C-4 |
| TD-5 | Test DB engine undecided; `inet`/`jsonb` not SQLite-portable | Test suite cannot be written | P0-12 / C-5 |
| TD-6 | Salary and leave overlap rules have no DB enforcement | Concurrency defeats app-level checks; violates `05` §5 | P0-5 (`btree_gist`) / C-11 |
| TD-7 | No reproducible local services (PostgreSQL/Redis) | Environment drift between dev, test, CI | P0-5 |
| TD-8 | Host PHP/Composer/PostgreSQL versions unverified | Package selection in later phases may be invalid | P0-3 |

---

## 9. Audit Boundaries

What this audit did **not** do, by instruction:

- No Phase 0 implementation. No Laravel installed, no dependency added, no migration written.
- No existing code rewritten — there is none.
- Nothing marked DONE. `04_IMPLEMENTATION_STATUS.md` was updated to state verified facts only, all of which are 0% / NOT STARTED.
- Host toolchain versions are recorded as **UNVERIFIED**, not guessed (§1.5).

---

## 10. Next Action

**TASK A1 — Phase 0**, but only after P0-1 and P0-2.

P0-1 is now mostly done — the owner initialised the repository and committed the blueprint at `55eeca2`. What remains of it is `.gitignore` (a hard prerequisite for the Laravel install) and committing this audit.

P0-2 is untouched and is the real gate. Closing C-1 … C-6 is documentation and process work costing under an hour, and it prevents rework across five later phases. Writing migrations before C-3, C-4 and C-5 are decided guarantees they get rewritten.

**Audit hygiene note:** this document was published with a stale claim about version control because a probe taken at 14:06 was treated as a standing fact at 14:10, while the owner was actively working in the same repository. Any future phase must re-verify repository state immediately before drawing a conclusion from it, and immediately before committing.


---

# 11. Re-audit — 2026-09-07 (post Laravel install)

Triggered by commit `408eb13` *"Add console and web routes, implement settings and authentication
features"*, which landed beneath this session's commit while P0-4 preparation was being written.
This is the second time repository state changed mid-task; the process note in §1.2 applies.

## 11.1 What is now installed

The owner installed the **`laravel/livewire-starter-kit`**, not plain `laravel/laravel`. This is a
different and better starting point than the runbook assumed, and it resolves several Phase 0 items
that had been planned as work.

| Item | Verified value | Evidence |
|---|---|---|
| Laravel | **13.17** (`laravel/framework ^13.17`) | `composer.json` |
| PHP constraint | **^8.3** (host runs 8.4.11) | `composer.json` |
| Frontend | **Livewire 4.1 + Flux 2.13 + Blaze**, Vite | `composer.json`, `vite.config.js` |
| Auth | **Fortify 1.37** — incl. passkeys and 2FA | `app/Providers/FortifyServiceProvider.php`, migrations |
| Testing | **Pest 5** + pest-plugin-laravel | `composer.json`, `tests/Pest.php` |
| Static analysis | **Larastan 3.9 at level 7** | `phpstan.neon` |
| Formatter | **Pint 1.27** | `pint.json` |
| Agent guidance | **Laravel Boost 2.2** — `AGENTS.md`, `.agents/skills/` (9 skills) | repository root |
| Database default | **sqlite** | `config/database.php`, `.env.example` |
| Cache / queue / session | **database** driver, not Redis | `.env.example` |

**Migrations present (5):** `create_users_table`, `create_cache_table`, `create_jobs_table`,
`create_passkeys_table`, `add_two_factor_columns_to_users_table`.

**Models present (1):** `User`.

**Tests present (10 files):** auth (authentication, registration, password reset/confirmation, email
verification, 2FA challenge), dashboard, settings (profile, security), plus two examples. **These were
not written by this session and have not been executed by it** — no pass/fail claim is made. See §11.4.

**Still absent:** RBAC (no `spatie/laravel-permission`), audit logging, domain layer, enums, scope
resolver, and all ~30 business tables.

## 11.2 Phase 0 items resolved by the starter kit

These were planned work and are now done, by installation rather than by this session:

- P0-4 Laravel install — **done** (`408eb13`).
- P0-6 frontend stack — **done**, and it confirms ADR-0007's direction.
- P0-7 authentication — **done and exceeded.** Fortify with passkeys and 2FA is materially stronger
  than the Breeze-or-hand-rolled option P0-7 contemplated.
- P0-12 test harness — **partly done.** Pest is configured with real auth tests; the engine is wrong
  (§11.3).
- P0-13 formatter and static analysis — **done and exceeded.** P0-13 recommended Larastan level 5 as a
  starting point; the project is configured at **level 7**. That recommendation is withdrawn — level 7
  is stricter and there is no reason to lower it.

## 11.3 Conflicts introduced by the starter kit

Five, all in configuration, all cheap to fix now and expensive later.

### R-1 — Test suite runs on SQLite in memory  *(severity: HIGH — violates ADR-0005)*

```xml
<env name="DB_CONNECTION" value="sqlite"/>
<env name="DB_DATABASE" value=":memory:"/>
```

This is the exact configuration ADR-0005 rejects. The schema requires `inet`, `jsonb`,
`numeric(10,7)` and `EXCLUDE USING gist`; SQLite implements none of them. Left as-is, the first
Phase 4 or Phase 7 migration either fails under test or, worse, passes while exercising different
type semantics than production.

**Fix:** point `phpunit.xml` at the PostgreSQL 17 container. The suite then requires
`docker compose up -d` before it runs — that is the accepted cost of ADR-0005.

### R-2 — Default database connection is SQLite  *(severity: HIGH)*

`config/database.php` → `env('DB_CONNECTION', 'sqlite')`, and `.env.example` sets `DB_CONNECTION=sqlite`.
The blueprint specifies PostgreSQL throughout (`00` §1, `02` §1).

### R-3 — Cache, queue and session use the database driver, not Redis  *(severity: HIGH)*

```dotenv
CACHE_STORE=database
QUEUE_CONNECTION=database
SESSION_DRIVER=database
```

`00` §1 specifies Redis for cache and queue, and Phase 0's exit criteria require a working queue.
Phase 9 (Telegram) requires queued delivery, and `05` §8 requires Telegram failure not to fail
attendance recording — which depends on the queue being real infrastructure.

### R-4 — Redis port and client mismatch  *(severity: medium)*

`.env.example` has `REDIS_PORT=6379` and `REDIS_CLIENT=phpredis`. The `docker-compose.yml` added this
session maps Redis to host port **6380** deliberately, to avoid the Homebrew Redis already on 6379.
Separately, `phpredis` is a PHP extension whose presence is **still unverified** (P0-3b). If it is
absent, `predis/predis` must be installed and `REDIS_CLIENT=predis` set — but note that AGENTS.md
forbids changing dependencies without approval, so that is a decision to confirm, not to take.

### R-5 — `APP_TIMEZONE` not set  *(severity: medium)*

`.env.example` has no `APP_TIMEZONE`. `02` §1 requires UTC storage with display timezone resolved
from company settings. The default must be pinned explicitly to `UTC` before any timestamp is
written, because retrofitting it means reinterpreting every stored value.

## 11.4 Working-method conflicts

Three, and they change how the rest of Phase 0 must be executed.

### M-1 — The assisting shell cannot run the toolchain AGENTS.md assumes

`AGENTS.md` instructs: create files with `php artisan make:`, run `vendor/bin/pint --dirty` after
every PHP edit, run `php artisan test --compact`, and prefer Laravel Boost MCP tools.

**None of that is possible from the assisting shell** — it is a Linux VM with no PHP, no Composer and
no network egress (§1.5). So work authored here is written directly as files rather than generated by
`artisan make:`, and it arrives **unformatted and untested**.

Two viable arrangements:
1. Files are authored here; the owner runs `vendor/bin/pint --dirty --format agent` and
   `php artisan test --compact` on the Mac and pastes results back. Slow, but works.
2. **Preferred:** a Laravel-Boost-capable agent runs on the Mac for code generation, where artisan,
   Pint, Pest and the Boost MCP tools all work, and this session is used for audit, architecture and
   review — which is what it can actually verify.

Either way, per `05_IMPLEMENTATION_RULES.md` §14 and AGENTS.md's test-enforcement rule, **no Phase 0
item may be marked DONE on this session's word.** Test output must come from the Mac or CI.

### M-2 — Two competing conventions for recorded decisions

`AGENTS.md` directs durable rules to `.ai/rules` via Boost's `record-rule` tool, mapping globs to rule
files. This session recorded decisions in `docs/adr/` instead, which is where the blueprint's own
documentation conventions point (`05` §13).

`.ai/rules` **does not currently exist**. Nothing is broken, but the project now has two intended
homes for standing constraints, and a future Boost-driven agent will read `.ai/rules` and never see
ADR-0002's scoping requirement — which is the single most important constraint in the project.

**Recommendation:** keep the ADRs as the reasoned record, and add a thin `.ai/rules/index.md` whose
rule files carry the binding one-line constraints and cite the ADR for rationale. That way the Boost
agent inherits them. This is a change to make with `record-rule` on the Mac, not by hand here.

### M-3 — Documentation policy tension

`AGENTS.md`: *"You must only create documentation files if explicitly requested by the user."*

This session created `REPOSITORY_AUDIT.md` (explicitly requested), updated `04_IMPLEMENTATION_STATUS.md`
(explicitly requested, and mandated by `05` §13), and created `docs/adr/` plus `PHASE0_RUNBOOK.md` on
its own initiative under P0-2. The ADRs were the deliverable P0-2 called for and the owner approved
P0-2 by instructing work to continue — but the runbook is now largely obsolete, since its central step
was installing Laravel.

**Going forward: no further documentation files without an explicit request.** Existing ones are
amended in place.

## 11.5 Unverified

Stated for honesty rather than left implied:

- **The test suite has not been run by this session** and cannot be. No claim is made about whether
  the ten existing test files pass.
- **P0-3b remains open** — PHP extensions (`pdo_pgsql`, `bcmath`, `intl`, `zip`, `gd`, `redis`) and
  running services are unconfirmed. `bcmath` and `pdo_pgsql` block Phase 0.
- **`laravel/chisel`, `livewire/blaze`, `laravel/pao` and `laravel/boost`** are direct dependencies
  this session has no reliable knowledge of and no network to research. Their roles are not described
  here rather than guessed at. Confirm with `composer show --direct` on the Mac.
- Whether `408eb13` modified any starter-kit defaults beyond adding routes and settings features has
  not been diffed against a pristine starter kit, because that would require fetching one.

## 11.6 Recommended next step

Apply R-1 … R-5 as one focused configuration change — `phpunit.xml`, `.env.example`, `.env` — then
run `docker compose up -d` and `php artisan test --compact` on the Mac. That single result establishes
whether the PostgreSQL/Redis foundation actually works, which is Phase 0's real exit criterion and is
currently unknown.

Only after that: RBAC, seeders, the ADR-0002 scope resolver, audit foundation and enums.
