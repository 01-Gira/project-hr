# ADR-0002 — Authorization: capability via permissions, row scope via policies

- **Status:** Accepted
- **Date:** 2026-09-07
- **Resolves:** C-2 (TASK A0)
- **Severity:** Highest-cost item in the audit to postpone

## Context

`00_PROJECT_CONTEXT.md` §3.6 correctly requires permission checks over role-name checks:

```php
$user->can('attendance.correct');   // yes
$user->hasRole('hr');               // no
```

But `07_PERMISSION_MATRIX.md` expresses capability with cell values that are not booleans:

```text
team · own · restricted · optional · approve-team · request-own · lookup · read
```

`spatie/laravel-permission` grants a **flat, global** permission. `$user->can('attendance.view-team')`
returns true or false; it cannot say *which* team. Half the permission matrix is therefore not
expressible in the authorization package the blueprint selects. Permissions answer *what*, and nothing
in the current design answers *which rows*.

This surfaces in Phase 1 (employee list), Phase 4 (attendance list), Phase 6 (leave approval queue),
Phase 7 (salary list) and Phase 8 (every report). If the scoping mechanism is invented separately in
each of those phases, they will diverge, and a scoping bug in an authorization layer is a data leak.

## Decision

Authorization has two independent layers, and both are mandatory on every protected read:

**Layer 1 — capability. `spatie/laravel-permission`.**
Answers "may this user perform this kind of action at all". Permission names exactly as listed in
`02_DATABASE_DESIGN.md` §2. Enforced in Policies and Form Requests.

**Layer 2 — scope. Laravel Policies + Eloquent query scopes.**
Answers "over which rows". Every model exposing scoped data implements:

```php
interface ScopedToViewer
{
    public function scopeVisibleTo(Builder $query, User $viewer): Builder;
}
```

The matrix cells map to scope resolution, not to extra permissions:

| Matrix cell | Capability permission | Scope resolution |
|---|---|---|
| all | `*.view-all` | no filter |
| team | `*.view-team` | employee is in the viewer's management chain, or shares the viewer's `branch_id` / `department_id` per role config |
| own | `*.view-own` | `employee_profiles.user_id = viewer.id` |
| restricted | capability granted, sensitive columns withheld | column-level filtering in the Resource/DTO |
| read | view permission without the corresponding write permission | no filter, no mutation |
| optional | not granted by default; explicit per-deployment grant | — |

**The scope resolver contract is built in Phase 0**, before the models it filters exist. Phase 0
ships the interface, a `ResolvesTeamScope` service resolving the manager chain and branch/department
sets, and tests against fixture users. Phases 1–8 implement the interface per model.

**No list query may call `Model::all()` or an unscoped `paginate()`.** Static analysis should flag it.

## Consequences

- Phase 0 grows by roughly a day.
- Every list query in Phases 1, 4, 6, 7, 8 is written once, correctly.
- Deferring this is the most expensive option available: it forces a rewrite of every list query in
  five phases, under the worst conditions — after the UI depends on the current shape.

## Alternatives rejected

**Permission-per-scope** (`attendance.view.branch.3`). Permission table grows with the org chart;
re-assigning an employee to another branch requires rewriting permissions. Rejected.

**Team scoping in the UI only.** Directly violates `05_IMPLEMENTATION_RULES.md` §6 — "Do not rely on
hidden buttons alone" — and leaks on any direct URL or API call. Rejected.

**A tenancy package.** Solves company isolation, not manager-chain scoping, which is the actual
requirement here. Rejected as a substitute; may still be revisited for multi-company (ADR-0009).

## Reversibility

Low cost while Phase 0 is the only consumer. Very high cost after Phase 4.
