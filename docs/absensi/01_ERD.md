# Absensi — ERD

This file is the canonical high-level ERD for the project.

## 1. Core ERD

```mermaid
erDiagram
    USERS ||--o| EMPLOYEE_PROFILES : has
    USERS }o--o{ ROLES : assigned
    ROLES }o--o{ PERMISSIONS : contains

    COMPANIES ||--o{ BRANCHES : owns
    BRANCHES ||--o{ DEPARTMENTS : contains
    DEPARTMENTS ||--o{ POSITIONS : contains

    BRANCHES ||--o{ EMPLOYEE_PROFILES : assigned
    DEPARTMENTS ||--o{ EMPLOYEE_PROFILES : assigned
    POSITIONS ||--o{ EMPLOYEE_PROFILES : assigned
    EMPLOYEE_PROFILES ||--o| EMPLOYEE_PROFILES : manager_of

    EMPLOYEE_PROFILES ||--o{ EMPLOYEE_DEVICES : owns
    EMPLOYEE_PROFILES ||--o{ EMPLOYEE_DOCUMENTS : has

    ID_CARD_TEMPLATES ||--o{ EMPLOYEE_ID_CARDS : template
    EMPLOYEE_PROFILES ||--o{ EMPLOYEE_ID_CARDS : owns

    BRANCHES ||--o{ SHIFTS : has
    EMPLOYEE_PROFILES ||--o{ EMPLOYEE_SCHEDULES : scheduled
    SHIFTS ||--o{ EMPLOYEE_SCHEDULES : uses
    HOLIDAYS }o--|| COMPANIES : belongs
    BRANCHES ||--o{ HOLIDAYS : scopes

    BRANCHES ||--o{ ATTENDANCE_POLICIES : configures

    EMPLOYEE_PROFILES ||--o{ QR_SESSIONS : generates
    BRANCHES ||--o{ QR_SESSIONS : scopes

    EMPLOYEE_PROFILES ||--o{ ATTENDANCES : has
    EMPLOYEE_SCHEDULES ||--o{ ATTENDANCES : drives
    SHIFTS ||--o{ ATTENDANCES : references

    ATTENDANCES ||--o{ ATTENDANCE_EVENTS : contains
    EMPLOYEE_PROFILES ||--o{ ATTENDANCE_EVENTS : produces
    QR_SESSIONS ||--o{ ATTENDANCE_EVENTS : evidence

    BRANCHES ||--o{ LOCATIONS : owns
    LOCATIONS ||--o{ GEOFENCES : has
    EMPLOYEE_PROFILES }o--o{ GEOFENCES : allowed

    LEAVE_TYPES ||--o{ LEAVE_REQUESTS : classifies
    EMPLOYEE_PROFILES ||--o{ LEAVE_REQUESTS : requests

    ATTENDANCES ||--o{ ATTENDANCE_CORRECTION_REQUESTS : corrected
    EMPLOYEE_PROFILES ||--o{ ATTENDANCE_CORRECTION_REQUESTS : requests

    EMPLOYEE_PROFILES ||--o{ EMPLOYEE_SALARIES : has_history
    EMPLOYEE_SALARIES ||--o{ EMPLOYEE_SALARY_COMPONENTS : contains
    SALARY_COMPONENTS ||--o{ EMPLOYEE_SALARY_COMPONENTS : defines

    PAYROLL_PERIODS ||--o{ PAYROLLS : contains
    EMPLOYEE_PROFILES ||--o{ PAYROLLS : receives
    PAYROLLS ||--o{ PAYROLL_ITEMS : contains
    SALARY_COMPONENTS ||--o{ PAYROLL_ITEMS : references

    USERS ||--o| TELEGRAM_USERS : links
    TELEGRAM_USERS ||--o{ TELEGRAM_NOTIFICATION_SUBSCRIPTIONS : subscribes
    TELEGRAM_USERS ||--o{ TELEGRAM_DELIVERY_LOGS : receives

    USERS ||--o{ AUDIT_LOGS : actor
```

---

## 2. Attendance Flow ERD

```mermaid
erDiagram
    EMPLOYEE_PROFILES ||--o{ QR_SESSIONS : generates
    QR_SESSIONS ||--o| ATTENDANCE_EVENTS : can_produce
    EMPLOYEE_PROFILES ||--o{ ATTENDANCE_EVENTS : produces
    ATTENDANCES ||--o{ ATTENDANCE_EVENTS : aggregates
    EMPLOYEE_SCHEDULES ||--o| ATTENDANCES : determines
    SHIFTS ||--o{ EMPLOYEE_SCHEDULES : configures
    GEOFENCES ||--o{ ATTENDANCE_EVENTS : validates
    ATTENDANCES ||--o{ ATTENDANCE_CORRECTION_REQUESTS : corrected_by
```

---

## 3. Salary / Payroll ERD

```mermaid
erDiagram
    EMPLOYEE_PROFILES ||--o{ EMPLOYEE_SALARIES : salary_history
    EMPLOYEE_SALARIES ||--o{ EMPLOYEE_SALARY_COMPONENTS : has
    SALARY_COMPONENTS ||--o{ EMPLOYEE_SALARY_COMPONENTS : component

    PAYROLL_PERIODS ||--o{ PAYROLLS : creates
    EMPLOYEE_PROFILES ||--o{ PAYROLLS : owns
    PAYROLLS ||--o{ PAYROLL_ITEMS : details
    SALARY_COMPONENTS ||--o{ PAYROLL_ITEMS : classifies
```

---

## 4. Telegram ERD

```mermaid
erDiagram
    USERS ||--o| TELEGRAM_USERS : linked_to
    TELEGRAM_USERS ||--o{ TELEGRAM_NOTIFICATION_SUBSCRIPTIONS : configures
    TELEGRAM_USERS ||--o{ TELEGRAM_DELIVERY_LOGS : receives
    BRANCHES ||--o{ TELEGRAM_NOTIFICATION_SUBSCRIPTIONS : scope
    DEPARTMENTS ||--o{ TELEGRAM_NOTIFICATION_SUBSCRIPTIONS : scope
```

---

## 5. Key Relationship Rules

1. `users` represents authentication identities.
2. `employee_profiles` represents workforce/HR identities.
3. A user may exist without an employee profile.
4. Attendance always references an employee profile.
5. One employee may have multiple salary records over time.
6. One employee may have multiple ID cards historically, but normally only one ACTIVE card.
7. One employee can generate many QR sessions.
8. `attendance_events` is append-only except for exceptional administrative recovery.
9. `attendances` has at most one logical daily attendance record per employee/schedule date.
10. Telegram authorization maps to an existing application user and inherits Laravel permissions.
