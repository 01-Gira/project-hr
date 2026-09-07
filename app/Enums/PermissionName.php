<?php

namespace App\Enums;

/**
 * The complete permission catalogue from 02_DATABASE_DESIGN.md §2.
 *
 * Named PermissionName rather than Permission to avoid colliding with
 * Spatie\Permission\Models\Permission wherever both are used.
 *
 * All 52 are seeded in Phase 0 even though most guard behaviour that does not
 * exist yet: this is the contract Phases 1-12 code against, and a permission
 * that appears only when its feature lands cannot be assigned to a role in
 * advance or asserted on in a policy test.
 *
 * 00_PROJECT_CONTEXT.md §3.6 — application code checks permissions, never role
 * names. Row-level scope is a separate concern handled by policies and query
 * scopes; see ADR-0002.
 */
enum PermissionName: string
{
    case DashboardView = 'dashboard.view';

    case UsersView = 'users.view';
    case UsersCreate = 'users.create';
    case UsersUpdate = 'users.update';
    case UsersDelete = 'users.delete';

    case EmployeesView = 'employees.view';
    case EmployeesCreate = 'employees.create';
    case EmployeesUpdate = 'employees.update';
    case EmployeesDelete = 'employees.delete';

    case RolesView = 'roles.view';
    case RolesCreate = 'roles.create';
    case RolesUpdate = 'roles.update';
    case RolesDelete = 'roles.delete';
    case PermissionsView = 'permissions.view';
    case PermissionsAssign = 'permissions.assign';

    case IdCardView = 'id-card.view';
    case IdCardGenerate = 'id-card.generate';
    case IdCardPrint = 'id-card.print';
    case IdCardRevoke = 'id-card.revoke';

    case AttendanceViewOwn = 'attendance.view-own';
    case AttendanceViewTeam = 'attendance.view-team';
    case AttendanceViewAll = 'attendance.view-all';
    case AttendanceScan = 'attendance.scan';
    case AttendanceCorrect = 'attendance.correct';
    case AttendanceApprove = 'attendance.approve';
    case AttendanceExport = 'attendance.export';

    case ScheduleView = 'schedule.view';
    case ScheduleManage = 'schedule.manage';

    case LeaveViewOwn = 'leave.view-own';
    case LeaveViewTeam = 'leave.view-team';
    case LeaveViewAll = 'leave.view-all';
    case LeaveCreate = 'leave.create';
    case LeaveApprove = 'leave.approve';

    case SalaryViewOwn = 'salary.view-own';
    case SalaryViewTeam = 'salary.view-team';
    case SalaryViewAll = 'salary.view-all';
    case SalaryCreate = 'salary.create';
    case SalaryUpdate = 'salary.update';
    case SalaryHistory = 'salary.history';

    case PayrollView = 'payroll.view';
    case PayrollProcess = 'payroll.process';
    case PayrollApprove = 'payroll.approve';
    case PayrollExport = 'payroll.export';

    case TelegramAccess = 'telegram.access';
    case TelegramManage = 'telegram.manage';
    case TelegramNotifications = 'telegram.notifications';
    case TelegramReports = 'telegram.reports';

    case SettingsView = 'settings.view';
    case SettingsUpdate = 'settings.update';

    case AuditView = 'audit.view';

    case ReportsView = 'reports.view';
    case ReportsExport = 'reports.export';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_map(static fn (self $case): string => $case->value, self::cases());
    }
}
