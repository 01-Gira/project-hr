# Authorization

**Globs:** `app/Models/**`, `app/Policies/**`, `app/Livewire/**`, `app/Http/**`, `database/seeders/**`

## Permissions grant capability; policies and query scopes grant reach

`spatie/laravel-permission` is flat. `$user->can('attendance.view-team')` answers
whether a capability is held, never **over which rows**. `07_PERMISSION_MATRIX.md`
marks cells `team`, `own`, `restricted` and `optional`, so half the matrix is not
expressible in permissions alone.

- Capability: `PermissionName` + `$user->can(...)`.
- Reach: `ResolveScopeLevel` → `ScopeLevel`, applied by the model's
  `scopeVisibleTo()` from the `ScopedToViewer` contract.

**A bare `Model::all()` or an unscoped `paginate()` on a scoped model is a data
leak, not a style problem.** The permission check upstream only established that
the viewer may see *something* of that type.

See ADR-0002.

## Check permissions, never role names

`$user->can('attendance.correct')`, never `$user->hasRole('hr')`. Roles group
permissions; they carry no business meaning and the role list is configurable.
See `00_PROJECT_CONTEXT.md` §3.6.

## Authorize on the backend, always

Hiding a menu item is not authorization. Every protected action needs a server
side check, and sensitive data must never be returned before it. See
`05_IMPLEMENTATION_RULES.md` §6.

## Salary permission is separate from employee permission

Being able to view an employee never implies being able to view their pay.
`salary.view-own` is **not** granted to the employee role by default, and
`hr-admin` does not receive `salary.view-all` or `salary.update`. See ADR-0010.

## Seeders install, they do not reconcile

`RoleSeeder` applies the matrix only when a role is first created. Never change
it to `syncPermissions()` or to grant on every run: both silently overwrite
administrator configuration. Granting an existing role new access belongs in an
explicit migration. super-admin is the sole exception and is synced to every
permission.
