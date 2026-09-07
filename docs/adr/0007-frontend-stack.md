# ADR-0007 — Frontend stack

- **Status:** Proposed — needs owner acceptance before P0-4
- **Date:** 2026-09-07
- **Closes pending decision:** "Blade/Livewire versus Inertia", "Primary admin UI component library"

## Decision

**Blade + Livewire 3 + Alpine.js + Tailwind CSS. Hand-built components. No admin framework.**

## Why Livewire over Inertia

The screens that carry this product's risk are stateful and server-authoritative:

- the QR screen (`08` §6) — a live countdown that must invalidate server-side and auto-refresh;
- the scanner (`08` §7) — repeated scans, each returning a specific server-decided outcome
  ("QR expired", "Outside geofence", "QR already used"), immediately ready for the next scan;
- live attendance (`08` §8) — counters and a filtered table refreshing against server state.

Every one of these is a round-trip to authoritative server logic. Livewire expresses them without a
client-side state layer or an API surface. Inertia + Vue/React would require both, plus duplicating
the permission model client-side to decide what renders — and `05` §6 warns that hidden buttons are
not authorization.

No mobile client exists until Phase 12, so there is no second consumer forcing an API now. Phase 12
adds a versioned API over the same domain Actions, which is where the API belongs regardless.

## Why no Filament / Nova

Filament would ship Phases 1–3 noticeably faster — CRUD over companies, branches, departments,
positions and employees is exactly its strength, and that is a real, quantifiable saving.

It is still the wrong trade here:

1. **The high-risk phases fight the abstraction.** The scanner, the geofence map editor, the QR
   lifecycle and the Telegram control room are not resources with forms. They would be escape-hatch
   custom pages inside a framework whose value is the abstraction being escaped.
2. **It pulls business logic into resource classes.** `05` §3 asks for domain Actions with thin
   presentation. Filament's idiom puts validation and mutation in the resource, which is the
   "giant controller" pattern `00` §7 explicitly rejects, relocated.
3. **Row-level scoping (ADR-0002) has to be reimplemented inside its query layer**, per resource, and
   a scoping mistake there is a salary leak.
4. **Salary and attendance UI would be coupled to a third-party upgrade cycle** for the lifetime of
   the product.

The honest summary: Filament wins Phases 1–3 and loses Phases 4, 5, 7 and 9. The later phases are
where correctness matters.

## Consequences

- Phases 1–3 are slower to build. This is the accepted cost.
- Tailwind components are hand-built once and reused; budget for that in Phase 0's app shell.
- One language, one auth model, no client-side permission duplication.

## Reversibility

Adding Inertia later for a specific area is possible. Removing an admin framework once Phases 1–3
are built on it is not — which is the asymmetry driving this recommendation.
