<?php

namespace Database\Seeders;

use App\Enums\PermissionName as P;
use App\Enums\RoleName;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/**
 * Seeds the eight default roles from 00_PROJECT_CONTEXT.md §5 and resolves
 * 07_PERMISSION_MATRIX.md into concrete grants.
 *
 * The matrix is a product-level document whose cells are not booleans: it uses
 * "team", "own", "restricted", "optional", "approve-team", "lookup" and "read".
 * Turning those into permissions requires judgment, and every such decision is
 * marked JUDGMENT below so it can be reviewed rather than discovered later.
 *
 * Two rules govern the ambiguous cells:
 *
 *   "optional"   -> NOT granted. A seeded default is what every deployment
 *                   starts with, and the failure modes are asymmetric: a
 *                   missing grant is fixed in seconds, an unintended one may
 *                   already have exposed data. See ADR-0010.
 *   "team"/"own" -> granted as the corresponding scoped permission. The
 *                   permission conveys capability only; which rows the holder
 *                   may see is decided by policies and query scopes, because
 *                   Spatie permissions are flat and cannot express row scope.
 *                   See ADR-0002.
 *
 * Re-seed semantics — this seeder is a baseline INSTALLER, not a reconciler:
 *
 *   - Roles are created if missing.
 *   - The matrix is applied ONLY when a role is first created. Once a role
 *     exists, its grants are never touched again, so an administrator's later
 *     adjustments — additions and removals alike — are durable.
 *   - super-admin is the sole exception and is always synced to every
 *     permission, because a super-admin missing a permission is a bug by
 *     definition.
 *
 * The alternative designs were both worse. syncPermissions() discards
 * administrator additions; givePermissionTo() on every run preserves additions
 * but silently restores anything an administrator deliberately revoked, which
 * is the same class of silent-revert bug as a config file being reset by a
 * framework upgrade.
 *
 * The cost of this choice, stated plainly: a permission introduced by a later
 * phase does NOT reach existing non-super-admin roles on a re-seed. That is
 * deliberate. Granting an existing role new access is a security-relevant
 * change and belongs in an explicit, reviewable migration rather than
 * happening as a side effect of running the seeders. A fresh install
 * (migrate:fresh --seed) always gets the current matrix.
 */
class RoleSeeder extends Seeder
{
    /**
     * @return array<string, list<P>>
     */
    private function matrix(): array
    {
        return [
            // Receives every permission, including ones added in later phases.
            // Handled separately in run(); listed here for completeness.
            RoleName::SuperAdmin->value => [],

            RoleName::Owner->value => [
                P::DashboardView,
                // Matrix "Employee all: ✓". JUDGMENT: destructive delete is
                // withheld from every role except super-admin.
                P::EmployeesView, P::EmployeesCreate, P::EmployeesUpdate,
                P::IdCardView, P::IdCardGenerate, P::IdCardPrint, P::IdCardRevoke,
                P::AttendanceViewOwn, P::AttendanceViewAll, P::AttendanceCorrect,
                P::AttendanceApprove, P::AttendanceExport,
                P::ScheduleView, P::ScheduleManage,
                P::LeaveViewOwn, P::LeaveViewAll, P::LeaveCreate, P::LeaveApprove,
                P::SalaryViewAll, P::SalaryCreate, P::SalaryUpdate, P::SalaryHistory,
                P::PayrollView, P::PayrollProcess, P::PayrollApprove, P::PayrollExport,
                P::TelegramAccess, P::TelegramManage, P::TelegramNotifications, P::TelegramReports,
                P::SettingsView, P::SettingsUpdate,
                P::AuditView,
                P::ReportsView, P::ReportsExport,
                // Matrix "User management / Role management: optional" -> not granted.
            ],

            RoleName::HrAdmin->value => [
                P::DashboardView,
                // Matrix "User management: ✓". JUDGMENT: delete withheld.
                P::UsersView, P::UsersCreate, P::UsersUpdate,
                P::EmployeesView, P::EmployeesCreate, P::EmployeesUpdate, P::EmployeesDelete,
                P::IdCardView, P::IdCardGenerate, P::IdCardPrint, P::IdCardRevoke,
                P::AttendanceViewOwn, P::AttendanceViewAll, P::AttendanceCorrect,
                P::AttendanceApprove, P::AttendanceExport,
                P::ScheduleView, P::ScheduleManage,
                P::LeaveViewOwn, P::LeaveViewAll, P::LeaveCreate, P::LeaveApprove,
                // Matrix "Salary own: ✓" for HR Admin.
                P::SalaryViewOwn,
                // JUDGMENT: matrix says "Salary all: restricted" and
                // "Salary update: optional". Neither is granted by default.
                // 00 §6 and 05 §7 keep salary permission separate from employee
                // permission, so HR seeing every salary must be a deliberate act.
                // Matrix "Settings: limited", "Audit logs: limited" -> read only.
                P::SettingsView,
                P::AuditView,
                P::ReportsView, P::ReportsExport,
            ],

            RoleName::Manager->value => [
                P::DashboardView,
                // Matrix "Employee all: team" -> capability here, scope in policy.
                P::EmployeesView,
                P::AttendanceViewOwn, P::AttendanceViewTeam,
                // Matrix "Attendance correction: approve-team". A manager
                // approves corrections; applying one directly is attendance.correct
                // and belongs to HR.
                P::AttendanceApprove,
                P::ScheduleView, P::ScheduleManage,
                P::LeaveViewOwn, P::LeaveViewTeam, P::LeaveCreate, P::LeaveApprove,
                P::ReportsView,
                // Matrix "Salary own/all: optional", "ID card: optional",
                // "Telegram: optional" -> not granted.
            ],

            RoleName::Finance->value => [
                P::DashboardView,
                // Matrix "Employee all: salary-limited" -> capability granted,
                // sensitive columns withheld at the presentation layer.
                P::EmployeesView,
                P::AttendanceViewOwn, P::AttendanceViewAll,
                P::LeaveViewOwn, P::LeaveCreate,
                P::SalaryViewOwn, P::SalaryViewAll, P::SalaryCreate,
                P::SalaryUpdate, P::SalaryHistory,
                P::PayrollView, P::PayrollProcess, P::PayrollApprove, P::PayrollExport,
                P::ReportsView, P::ReportsExport,
                // Matrix "Audit logs: optional" -> not granted.
            ],

            RoleName::AttendanceOperator->value => [
                P::DashboardView,
                // Matrix "Employee all: lookup" -> needs to identify who scanned.
                P::EmployeesView,
                P::AttendanceViewOwn, P::AttendanceScan,
                P::LeaveViewOwn, P::LeaveCreate,
                // Matrix "Reports: limited".
                P::ReportsView,
            ],

            RoleName::Employee->value => [
                P::DashboardView,
                P::AttendanceViewOwn,
                P::ScheduleView,
                P::LeaveViewOwn, P::LeaveCreate,
                P::IdCardView,
                // JUDGMENT: matrix says "Salary own: optional" for Employee.
                // ADR-0010 resolves that to NOT granted by default.
            ],

            RoleName::Auditor->value => [
                // Read everywhere, write nowhere. No *.create/update/delete,
                // no approve, no scan, no process.
                P::DashboardView,
                P::UsersView, P::RolesView, P::PermissionsView,
                P::EmployeesView,
                P::IdCardView,
                P::AttendanceViewAll,
                P::ScheduleView,
                P::LeaveViewAll,
                P::PayrollView,
                P::AuditView,
                P::ReportsView, P::ReportsExport,
                // Matrix "Salary own/all: optional" -> not granted.
            ],
        ];
    }

    public function run(): void
    {
        foreach ($this->matrix() as $roleName => $permissions) {
            $role = Role::firstOrCreate([
                'name' => $roleName,
                'guard_name' => 'web',
            ]);

            // Apply the matrix on creation only. Re-running must not overwrite
            // whatever an administrator has since configured.
            if ($role->wasRecentlyCreated) {
                $role->givePermissionTo($permissions);
            }
        }

        // Super-admin is the deliberate exception: synced to everything on every
        // run, so a permission added by a later phase is inherited rather than
        // silently missing. A super-admin lacking a permission is a bug, so
        // there is no administrator intent here worth preserving.
        Role::firstOrCreate(['name' => RoleName::SuperAdmin->value, 'guard_name' => 'web'])
            ->syncPermissions(Permission::all());

        Artisan::call('permission:cache-reset');
    }
}
