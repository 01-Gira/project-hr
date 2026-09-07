# Absensi — UI/UX Blueprint

## 1. Design Direction

- responsive;
- mobile-first for employee;
- desktop-first but responsive for admin;
- simple operational dashboard;
- minimal clicks for attendance;
- status should be understandable in seconds;
- avoid excessive modals;
- sensitive modules visually separated.

---

# 2. Employee Navigation

```text
Dashboard
Attendance
Schedule
Leave
History
ID Card
Profile
```

Optional:

```text
Salary
Payslip
```

only if employee has permission.

---

# 3. Employee Dashboard

Priority order:

1. current time/date;
2. today shift;
3. attendance status;
4. generate QR action;
5. check-in/check-out values;
6. exception/request shortcuts;
7. recent history.

Example:

```text
┌─────────────────────────────────┐
│ Good Morning, Gira              │
│ Monday, 7 September 2026        │
├─────────────────────────────────┤
│ TODAY                           │
│ Office • 08:00 - 17:00          │
│                                 │
│ NOT CHECKED IN                  │
│                                 │
│       [ GENERATE QR ]           │
├─────────────────────────────────┤
│ Check In     --                 │
│ Check Out    --                 │
│ Work Time    --                 │
├─────────────────────────────────┤
│ Attendance | Leave | History    │
└─────────────────────────────────┘
```

---

# 4. Admin Sidebar

```text
Dashboard

ATTENDANCE
- Live Attendance
- Attendance Records
- Corrections

WORKFORCE
- Employees
- ID Cards
- Departments
- Positions
- Branches

SCHEDULING
- Shifts
- Schedules
- Holidays

TIME OFF
- Leave Requests
- Leave Types

COMPENSATION
- Employee Salary
- Salary Components
- Payroll

REPORTS
- Attendance
- Late
- Absence
- Work Hours
- Payroll

INTEGRATIONS
- Telegram

SYSTEM
- Users
- Roles
- Permissions
- Locations / Geofences
- Settings
- Audit Logs
```

Hide menus based on permission.

---

# 5. Employee Detail

```text
[Overview]
[Attendance]
[Schedule]
[Leave]
[Salary]
[ID Card]
[Documents]
[Devices]
[Audit]
```

Only render sensitive tabs if authorized.

---

# 6. QR Screen

```text
┌────────────────────────────┐
│ Attendance QR              │
│                            │
│        [ QR CODE ]         │
│                            │
│        00:38               │
│                            │
│ Expires automatically      │
│ Employee: EMP-000014       │
└────────────────────────────┘
```

When expired:

- invalidate old token;
- refresh automatically if policy allows;
- show loading/error state.

---

# 7. Scanner Screen

Scanner page should optimize repeated scans.

```text
┌─────────────────────────────────┐
│ Scan Attendance QR              │
│                                 │
│          CAMERA AREA            │
│                                 │
├─────────────────────────────────┤
│ Last Scan                       │
│ ✓ Gira Muhammad                 │
│ CHECK IN • 07:58                │
│ ON TIME                         │
└─────────────────────────────────┘
```

After success, immediately prepare for next scan.

Error examples must be specific:

```text
QR expired.
Outside geofence.
QR already used.
Employee inactive.
No valid schedule.
Location accuracy too low.
```

---

# 8. Live Attendance

Cards:

```text
Employees
Present
Late
Absent
Leave
Missing Checkout
```

Table:

```text
Employee
Branch
Department
Shift
Check In
Check Out
Status
```

Filters:

```text
Date
Branch
Department
Status
Search
```

---

# 9. Geofence UI

Admin needs:

- location name;
- map pin;
- lat/lng;
- radius;
- active/inactive.

Map should show circle radius.

Do not require map provider-specific data in domain layer.

---

# 10. Salary UI

Sensitive UI.

Employee salary detail:

```text
Basic Salary
Fixed Earnings
Fixed Deductions
Effective Date
History
```

Always show effective period.

Do not overwrite old record through ordinary edit UX without explicit historical behavior.

---

# 11. Telegram UI

Settings:

```text
Bot Status
Authorized Users
Pairing
Notification Subscriptions
Report Schedule
Delivery Logs
```

Subscription fields:

```text
Notification Type
Delivery Mode
Branch
Department
Enabled
Schedule Time
```

---

# 12. Responsive Requirement

Minimum target widths:

```text
mobile      360px+
tablet      768px+
desktop     1280px+
```

Tables should:

- use responsive columns;
- allow horizontal scroll where needed;
- provide mobile cards for employee-facing screens when tables become unreadable.
