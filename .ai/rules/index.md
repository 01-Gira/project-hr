# Project rules index

Settled decisions and standing constraints for this codebase. Each rule file
states the binding constraint; the ADR it cites carries the reasoning, the
alternatives considered and the cost of reversing it.

Read every rule file whose globs cover the paths you are about to touch.

| Rule file | Applies to |
|---|---|
| [authorization.md](authorization.md) | `app/Models/**`, `app/Policies/**`, `app/Livewire/**`, `app/Http/**`, `database/seeders/**` |
| [database.md](database.md) | `database/migrations/**`, `config/database.php`, `phpunit.xml` |
| [audit.md](audit.md) | `app/Models/AuditLog.php`, `app/Actions/Audit/**`, anything performing a sensitive action |
| [domain-invariants.md](domain-invariants.md) | `app/Domain/**`, `app/Models/**`, `app/Actions/**` |

Full blueprint: `docs/absensi/`. Decision records: `docs/adr/`. Verified state
of the codebase: `docs/implementation/REPOSITORY_AUDIT.md`.

> Written by hand rather than through Boost's `record-rule`, because the session
> that authored them had no runtime. Prefer `record-rule` for additions.
