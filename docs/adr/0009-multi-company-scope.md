# ADR-0009 — Multi-company support in V1

- **Status:** Proposed — needs owner acceptance before P0-4
- **Date:** 2026-09-07
- **Closes pending decision:** "Multi-company support enabled in V1 or schema-only future readiness"

## Decision

**Schema-only readiness.** Carry `company_id` and its foreign key on every table the design already
places it on. Build no tenant switching, no tenant middleware, no per-tenant configuration, no
subdomain routing in V1.

## Why

The column is already in the design (`02` §3–§17) and costs 8 bytes plus an index. Retrofitting a
tenant discriminator later means migrating every table, backfilling every row, and revisiting every
query — the single most expensive schema change this project could face.

The machinery is the opposite: tenant switching, tenant-scoped config resolution and tenant-aware
caching are all additive, and building them before a second company exists means maintaining and
testing an abstraction with one instance — where bugs stay invisible precisely because there is
nothing to isolate against.

So: pay the cheap irreversible part now, defer the expensive reversible part.

## Consequences

- V1 seeds exactly one company. Code may assume a single current company, but must read it from a
  resolver (`CurrentCompany`) rather than hardcoding `company_id = 1`, so enabling tenancy later is a
  change to the resolver, not a search across the codebase.
- Global scopes for company isolation are **not** added in V1. When multi-company is enabled they are
  added alongside the tenant resolver, and ADR-0002's `scopeVisibleTo` composes with them.
- `02` §18 uniqueness is already company-scoped (`employee_profiles(company_id, employee_number)`,
  `salary_components(company_id, code)`). Keep it that way even with one company — those constraints
  are what make the later switch safe.

## Reversibility

The decision *not* to build tenancy machinery is fully reversible. The decision to carry `company_id`
is the part that would be expensive to reverse, which is why it is the part being done now.
