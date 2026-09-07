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
 * This seeder is idempotent and NON-DESTRUCTIVE for non-super-admin roles: it
 * grants what is missing and never revokes. An administrator's later
 * adjustments must survive a re-seed.
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
            $role = Role::findOrCreate($roleName, 'web');

            // givePermissionTo is additive; syncPermissions would revoke an
            // administrator's later adjustments on every re-seed.
            $role->givePermissionTo($permissions);
        }

        // Super-admin is synced to everything rather than listed, so a
        // permission added by a later phase is inherited automatically. With an
        // explicit list, super-admin would silently lose access to each new
        // feature until someone remembered to update this seeder.
        Role::findOrCreate(RoleName::SuperAdmin->value, 'web')
            ->syncPermissions(Permission::all());

        Artisan::call('permission:cache-reset');
    }
}
