# ADR-0010 — Salary is not visible to employees by default

- **Status:** Accepted 2026-09-07 (owner instruction to continue; was Proposed pending P0-8) (role seeder)
- **Date:** 2026-09-07
- **Closes pending decision:** "Whether salary is visible to employee by default"

## Decision

**`salary.view-own` is NOT granted to the `employee` role by the default seeder.** It is an explicit,
per-deployment grant.

## Why

`00_PROJECT_CONTEXT.md` §6 and `05_IMPLEMENTATION_RULES.md` §7 both state that salary permission is
separate from employee-profile permission, and `07_PERMISSION_MATRIX.md` marks the employee/salary-own
cell `optional` rather than `✓`. "Optional" in a seeder must resolve to off — a seeded default is what
every deployment starts with, and the failure modes are asymmetric:

- Default **off**, deployment wants it on → an administrator grants one permission. Recoverable in
  seconds.
- Default **on**, deployment did not want it → salary was already visible to every employee before
  anyone noticed. Not recoverable.

There is no reading of the blueprint under which the risky default is the correct one.

## Consequences

- `08_UI_UX_BLUEPRINT.md` §2 already lists Salary and Payslip as conditional employee nav items.
  They render only under `salary.view-own`, and the underlying route enforces it independently — the
  hidden menu item is not the control (`05` §6).
- The Phase 0 role seeder must be **idempotent and non-destructive**: re-running it must not re-grant
  or revoke permissions an administrator has since changed. Seed only what is missing.
- Phase 7 must include a test asserting an employee without `salary.view-own` receives 403 on their
  own salary endpoint — not merely that the link is hidden.

## Alternatives rejected

**Grant by default because employees generally may see their own pay.** Probably true as a product
statement, and it is the reason this is a genuine question rather than an obvious one. Rejected
anyway on the asymmetry above: it makes the irreversible outcome the default, and the blueprint's own
matrix says `optional`, not `✓`.
