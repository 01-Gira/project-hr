<?php

namespace App\Enums;

/**
 * Auditable actions.
 *
 * 05_IMPLEMENTATION_RULES.md §11 requires an audit entry for salary changes,
 * attendance corrections, role/permission changes, settings updates, ID card
 * revocation, Telegram pairing, manual attendance and payroll approval.
 *
 * Typed rather than free-form on purpose: an audit trail whose action names are
 * arbitrary strings cannot be queried or asserted on reliably. Later phases add
 * cases as they implement the behaviour, which keeps each addition reviewable.
 */
enum AuditAction: string
{
    case EmployeeCreated = 'EMPLOYEE_CREATED';
    case EmployeeUpdated = 'EMPLOYEE_UPDATED';
    case SalaryUpdated = 'SALARY_UPDATED';
    case AttendanceCorrected = 'ATTENDANCE_CORRECTED';
    case IdCardRevoked = 'ID_CARD_REVOKED';
    case RolePermissionChanged = 'ROLE_PERMISSION_CHANGED';
    case TelegramLinked = 'TELEGRAM_LINKED';
    case SettingChanged = 'SETTING_CHANGED';
}
