# Absensi — Permission Matrix

This is the initial permission model. Roles are configurable.

| Capability | Super Admin | Owner | HR Admin | Manager | Finance | Attendance Operator | Employee | Auditor |
|---|---:|---:|---:|---:|---:|---:|---:|---:|
| Dashboard | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ |
| User management | ✓ | optional | ✓ |  |  |  |  | read |
| Role management | ✓ | optional |  |  |  |  |  | read |
| Employee all | ✓ | ✓ | ✓ | team | salary-limited | lookup | own | read |
| ID card generate | ✓ | ✓ | ✓ | optional |  | optional |  | read |
| Attendance own | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | read |
| Attendance all | ✓ | ✓ | ✓ | team | read | scanner |  | read |
| Attendance correction | ✓ | ✓ | ✓ | approve-team |  |  | request-own | read |
| Schedule manage | ✓ | ✓ | ✓ | team |  |  |  | read |
| Leave request | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | read |
| Leave approve | ✓ | ✓ | ✓ | team |  |  |  | read |
| Salary own | ✓ | optional | ✓ | optional | ✓ |  | optional | optional |
| Salary all | ✓ | ✓ | restricted | optional | ✓ |  |  | optional |
| Salary update | ✓ | ✓ | optional |  | ✓ |  |  |  |
| Payroll process | ✓ | ✓ | optional |  | ✓ |  |  | read |
| Reports | ✓ | ✓ | ✓ | team | ✓ | limited | own | read |
| Telegram access | ✓ | ✓ | optional | optional | optional | optional | optional | optional |
| Telegram manage | ✓ | ✓ | optional |  |  |  |  |  |
| Settings | ✓ | ✓ | limited |  |  |  |  | read |
| Audit logs | ✓ | ✓ | limited |  | optional |  |  | ✓ |

## Important

This table is a product default only.

Implementation must use permissions rather than hardcoding these role names.

Suggested permission naming is defined in `02_DATABASE_DESIGN.md`.
