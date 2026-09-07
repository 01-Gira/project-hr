# ADR-0006 — employee_profiles naming and FK convention

- **Status:** Accepted
- **Date:** 2026-09-07
- **Resolves:** C-6, C-7 (TASK A0)

## Context

The blueprint is inconsistent with itself:

- `02_DATABASE_DESIGN.md` §4 defines the table as **`employee_profiles`**.
- `02_DATABASE_DESIGN.md` §18 writes the constraint as **`employees(company_id, employee_number)`**.
- `01_ERD.md` consistently uses `EMPLOYEE_PROFILES`.

Separately, every foreign key in the design is named `employee_id` (`attendances.employee_id`,
`qr_sessions.employee_id`, `employee_devices.employee_id`) while pointing at `employee_profiles`.
Laravel's Eloquent would infer `employee_profile_id` from the model name, so relationships silently
resolve to a column that does not exist.

## Decision

**Table name: `employee_profiles`.** §18 is corrected.

The name is not merely the majority spelling — it carries the distinction `01_ERD.md` §5 rules 1–3
depend on: `users` is an authentication identity, `employee_profiles` is a workforce identity, a user
may exist without one, and attendance always references the profile rather than the user. Calling the
table `employees` invites exactly the conflation those rules forbid.

**FK column name: `employee_id`, kept as-is.** "Employee" is the domain word; `employee_profile_id`
reads as ORM bookkeeping leaking into the schema.

**Therefore every relationship must be declared explicitly** — Eloquent inference is wrong here by
construction:

```php
// EmployeeProfile
public function attendances(): HasMany
{
    return $this->hasMany(Attendance::class, 'employee_id');
}

// Attendance
public function employee(): BelongsTo
{
    return $this->belongsTo(EmployeeProfile::class, 'employee_id');
}
```

The model class is `EmployeeProfile` with `protected $table = 'employee_profiles';` stated explicitly.

## Consequences

- One convention, stated once, applied everywhere.
- The risk is a forgotten explicit key silently resolving to `employee_profile_id`. **Larastan at
  level 5 or above catches this**, which is part of why P0-13 specifies that level.

## Alternatives rejected

**Rename the FK to `employee_profile_id`** so Eloquent inference works unaided. Removes a whole class
of mistake at the cost of a noisier schema and edits to roughly a dozen table definitions in `02`.
A defensible choice; rejected because the blueprint's own naming is already consistent on
`employee_id` and static analysis covers the failure mode.

**Rename the table to `employees`.** Rejected — it erodes the user/employee split (`01` §5).

## Reversibility

Cheap before Phase 1 migrations exist. Expensive afterwards.
