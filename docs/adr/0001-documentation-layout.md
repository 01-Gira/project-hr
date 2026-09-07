# ADR-0001 — Documentation layout

- **Status:** Accepted
- **Date:** 2026-09-07
- **Resolves:** C-1 (TASK A0)

## Context

`docs/README.md` and the suggested Codex prompt in `06_CODEX_TASKS.md` both referred to
`docs/absensi/`. The files actually sat at `docs/`. Every task prompt therefore instructed the
implementer to read a directory that did not exist. An agent following the prompt literally finds
nothing and proceeds on assumption — exactly the failure `05_IMPLEMENTATION_RULES.md` §1 forbids.

Two other document classes now need somewhere to live: audits (verified statements about the code)
and ADRs (decisions resolving blueprint ambiguity). Leaving all three flat in `docs/` would mix
specification with findings.

## Decision

```text
docs/absensi/          blueprint — the specification
docs/adr/              architecture decision records
docs/implementation/   audits and implementation reports
```

The ten blueprint documents move to `docs/absensi/` via `git mv`, preserving history. The path in
the blueprint becomes a statement of fact rather than a recommendation.

Precedence where the three disagree: **the audit wins on facts, the ADR wins on decisions, the
blueprint wins on intent.**

## Consequences

- Every existing prompt in `06_CODEX_TASKS.md` becomes correct without editing it.
- External references to `docs/0X_*.md` break. Only this repository's own documents referenced those,
  and they were updated in the same commit.

## Alternatives rejected

Editing `README.md` and `06_CODEX_TASKS.md` to say `docs/` instead. Cheaper, but it leaves the
blueprint with no namespace, so `docs/` accumulates audits, ADRs and specification side by side and
the distinction between "what we intend", "what we decided" and "what is true" erodes.

## Reversibility

Trivial — one `git mv` back.
