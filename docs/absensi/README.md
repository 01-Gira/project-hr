# Absensi Codex Blueprint

Read files in this order:

1. `00_PROJECT_CONTEXT.md`
2. `01_ERD.md`
3. `02_DATABASE_DESIGN.md`
4. `03_ROADMAP.md`
5. `04_IMPLEMENTATION_STATUS.md`
6. `05_IMPLEMENTATION_RULES.md`
7. `06_CODEX_TASKS.md`
8. `07_PERMISSION_MATRIX.md`
9. `08_UI_UX_BLUEPRINT.md`

## Location

These blueprint documents live at:

```text
docs/absensi/
```

Confirmed and enforced 2026-09-07 — see `docs/adr/0001-documentation-layout.md`. Earlier revisions
described this path as merely "recommended" while the files actually sat at `docs/`, which made every
task prompt in `06_CODEX_TASKS.md` point at a path that did not exist.

## Related directories

```text
docs/absensi/          blueprint (this directory) — the specification
docs/adr/              architecture decision records — what was decided and why
docs/implementation/   audits and implementation reports — what is actually true of the code
```

The blueprint states intent. The ADRs resolve its ambiguities. The audits state verified reality.
Where they disagree, the audit wins on facts and the ADR wins on decisions.

## Status

TASK A0 — Repository Audit is **complete**: `docs/implementation/REPOSITORY_AUDIT.md`.

Next is TASK A1 — Phase 0, gated on ADR-0001 … ADR-0006 being accepted.
