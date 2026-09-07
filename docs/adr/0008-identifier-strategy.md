# ADR-0008 — Primary keys and public identifiers

- **Status:** Accepted 2026-09-07 (owner instruction to continue; was Proposed pending P0-4)
- **Date:** 2026-09-07
- **Closes pending decision:** "UUID versus ULID as public identifier"

## Decision

**`bigint` auto-increment primary key + a `uuid` column (UUID v7) as the public identifier.**
URLs and any external reference use `uuid`. Foreign keys use the `bigint`.

This is the option `02_DATABASE_DESIGN.md` §1 already recommends; this ADR fixes the *version*, which
the blueprint leaves open, and that detail is the whole decision.

## Why UUID v7 specifically, not v4

UUID v4 is random, so as a primary key it scatters B-tree inserts across the index and fragments it.
That is the defect ULID was designed to fix, by making the identifier time-sortable.

UUID v7 is also time-sortable — it carries a millisecond timestamp prefix — so it obtains ULID's
insert locality **while remaining a native PostgreSQL `uuid` type**, with native storage (16 bytes),
native indexing and native comparison. ULID stored in PostgreSQL is either a 26-character string or a
custom extension.

So: v7 gives the ordering benefit that motivates ULID without leaving the native type. ULID's
remaining advantage is a friendlier textual form, which does not outweigh native typing here.

Laravel 11+ ships `Str::uuid7()`. Use a `HasUuid` trait generating it on `creating`.

## Why keep the bigint PK at all

The high-volume tables — `attendance_events`, `audit_logs`, `telegram_delivery_logs` — are append-heavy
and heavily indexed. `bigint` keys are 8 bytes against 16, and every foreign key in the schema pays
that difference in both the table and its indexes. Public exposure is handled by the `uuid` column, so
the enumeration concern that usually argues against sequential keys does not apply.

## Consequences

- Every table carries an extra 16-byte column and a unique index. Accepted.
- Route model binding must be configured to resolve on `uuid`:
  `public function getRouteKeyName(): string { return 'uuid'; }`
- **A sequential `id` must never appear in a URL, API payload or QR token.** QR tokens are separately
  required to be opaque random values (`00` §6) and are unrelated to this identifier.

## Alternatives rejected

**ULID as primary key.** Loses the native `uuid` type. Rejected.
**UUID v4 as primary key.** Index fragmentation on the highest-volume tables. Rejected.
**bigint only, no public identifier.** Exposes row counts and enables enumeration. Rejected.
