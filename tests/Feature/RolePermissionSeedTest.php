<?php

use App\Enums\PermissionName;
use App\Enums\RoleName;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/*
| Covers the Phase 0 RBAC seed: the 52 permissions from 02_DATABASE_DESIGN.md §2
| and the 8 roles from 00_PROJECT_CONTEXT.md §5, resolved from
| 07_PERMISSION_MATRIX.md per ADR-0002 and ADR-0010.
*/

beforeEach(function () {
    $this->seed([PermissionSeeder::class, RoleSeeder::class]);
});

test('every permission in the catalogue is seeded', function () {
    expect(Permission::count())->toBe(count(PermissionName::cases()))
        ->and(Permission::pluck('name')->sort()->values()->all())
        ->toBe(collect(PermissionName::values())->sort()->values()->all());
});

test('every default role is seeded', function () {
    expect(Role::pluck('name')->sort()->values()->all())
        ->toBe(collect(RoleName::values())->sort()->values()->all());
});

test('super admin holds every permission', function () {
    $role = Role::findByName(RoleName::SuperAdmin->value);

    expect($role->permissions)->toHaveCount(count(PermissionName::cases()));
});

test('super admin inherits permissions added after the initial seed', function () {
    // A later phase adds a permission. An explicit list would leave super-admin
    // silently locked out of it; the seeder syncs instead.
    Permission::findOrCreate('phase99.something', 'web');

    $this->seed(RoleSeeder::class);

    expect(Role::findByName(RoleName::SuperAdmin->value)->hasPermissionTo('phase99.something'))
        ->toBeTrue();
});

test('employees cannot see their own salary by default', function () {
    // ADR-0010: the matrix marks this "optional", and a seeded default is what
    // every deployment starts with. Default-on is unrecoverable once shipped.
    expect(Role::findByName(RoleName::Employee->value)
        ->hasPermissionTo(PermissionName::SalaryViewOwn->value))->toBeFalse();
});

test('the auditor role can read but never write', function () {
    $auditor = Role::findByName(RoleName::Auditor->value);

    $writes = collect(PermissionName::cases())
        ->filter(fn (PermissionName $p) => (bool) preg_match(
            '/\.(create|update|delete|assign|approve|process|scan|correct|generate|print|revoke|manage)$/',
            $p->value
        ));

    expect($writes)->not->toBeEmpty();

    foreach ($writes as $permission) {
        expect($auditor->hasPermissionTo($permission->value))
            ->toBeFalse("auditor unexpectedly holds {$permission->value}");
    }
});

test('a manager approves corrections but cannot apply them directly', function () {
    $manager = Role::findByName(RoleName::Manager->value);

    expect($manager->hasPermissionTo(PermissionName::AttendanceApprove->value))->toBeTrue()
        ->and($manager->hasPermissionTo(PermissionName::AttendanceCorrect->value))->toBeFalse();
});

test('hr admin does not receive blanket salary access by default', function () {
    $hr = Role::findByName(RoleName::HrAdmin->value);

    expect($hr->hasPermissionTo(PermissionName::SalaryViewOwn->value))->toBeTrue()
        ->and($hr->hasPermissionTo(PermissionName::SalaryViewAll->value))->toBeFalse()
        ->and($hr->hasPermissionTo(PermissionName::SalaryUpdate->value))->toBeFalse();
});

test('only super admin may delete users or employees', function () {
    foreach (RoleName::cases() as $role) {
        if ($role->isSuperAdmin()) {
            continue;
        }

        expect(Role::findByName($role->value)->hasPermissionTo(PermissionName::UsersDelete->value))
            ->toBeFalse("{$role->value} unexpectedly holds users.delete");
    }
});

test('re-seeding is idempotent', function () {
    $this->seed([PermissionSeeder::class, RoleSeeder::class]);

    expect(Permission::count())->toBe(count(PermissionName::cases()))
        ->and(Role::count())->toBe(count(RoleName::cases()));
});

test('re-seeding does not revoke an administrator adjustment', function () {
    $manager = Role::findByName(RoleName::Manager->value);
    $manager->revokePermissionTo(PermissionName::ReportsView->value);

    // An operator deliberately narrowed this role. A sync-based seeder would
    // silently restore it on the next deploy.
    $this->seed(RoleSeeder::class);

    expect(Role::findByName(RoleName::Manager->value)
        ->hasPermissionTo(PermissionName::ReportsView->value))->toBeFalse();
});

test('a user granted a role resolves its permissions', function () {
    $user = User::factory()->create();
    $user->assignRole(RoleName::AttendanceOperator->value);

    expect($user->can(PermissionName::AttendanceScan->value))->toBeTrue()
        ->and($user->can(PermissionName::SalaryViewAll->value))->toBeFalse();
});

test('seeded users receive a public identifier', function () {
    // Guards against WithoutModelEvents being reintroduced into DatabaseSeeder,
    // which would suppress the HasUuid creating hook and seed null uuids.
    $user = User::factory()->create();

    expect($user->uuid)->not->toBeNull();
});
