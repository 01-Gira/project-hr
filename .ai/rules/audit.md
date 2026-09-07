# Audit trail

**Globs:** `app/Models/AuditLog.php`, `app/Actions/Audit/**`, and any code performing a sensitive action

## Audit logs are append-only

`AuditLog` refuses updates and deletes, and the table has no `updated_at`. An
audit trail that can be quietly rewritten provides no accountability. Do not add
an update path, and do not relax the model guard to make a test easier.

`actor_user_id` is `nullOnDelete`, never `cascade`: removing a user must not
erase the record of what they did.

## Record through the action, with a typed action name

Use `RecordAuditLog`, and add a case to `AuditAction` rather than passing a
string. A trail whose action names are arbitrary cannot be queried or asserted
on.

## What must be audited

Salary create/update, attendance correction, role and permission changes,
settings updates, ID card revocation, Telegram pair/revoke, manual attendance,
payroll approval and lock. See `05_IMPLEMENTATION_RULES.md` §11.

## Request evidence travels in Context

`AssignRequestId` puts `audit.request_id` in visible context and the IP and user
agent in hidden context. `RecordAuditLog` reads from there, so it behaves the
same in HTTP requests, queued jobs and Artisan commands. Do not pass the
`Request` into domain services to obtain them. A console action records a null
actor — that is honest, not a gap to fill.
