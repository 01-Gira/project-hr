<?php

use App\Enums\PermissionName;
use App\Enums\RoleName;
use App\Enums\ScopeLevel;
use App\Models\User;
use App\Support\Authorization\ResolveScopeLevel;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;

/*
| ADR-0002. Permissions grant capability; this resolves how far that capability
| reaches. The employee tables arrive in Phase 1, so the "which team" half is
| not testable yet — the "how wide" half is, and it is the half every list query
| in Phases 1, 4, 6, 7 and 8 will depend on.
*/

beforeEach(function () {
    $this->seed([PermissionSeeder::class, RoleSeeder::class]);
    $this->resolve = app(ResolveScopeLevel::class);
});

test('a user with no view permission sees nothing', function () {
    $level = $this->resolve->handle(User::factory()->create(), 'attendance');

    expect($level)->toBe(ScopeLevel::None)
        ->and($level->seesAnything())->toBeFalse();
});

test('an employee is scoped to their own rows', function () {
    $user = User::factory()->create();
    $user->assignRole(RoleName::Employee->value);

    expect($this->resolve->handle($user, 'attendance'))->toBe(ScopeLevel::Own);
});

test('a manager is scoped to their team', function () {
    $user = User::factory()->create();
    $user->assignRole(RoleName::Manager->value);

    expect($this->resolve->handle($user, 'attendance'))->toBe(ScopeLevel::Team);
});

test('hr admin sees every row', function () {
    $user = User::factory()->create();
    $user->assignRole(RoleName::HrAdmin->value);

    expect($this->resolve->handle($user, 'attendance'))->toBe(ScopeLevel::All);
});

test('the widest permission held wins', function () {
    // A manager additionally granted view-all must not stay narrowed to their
    // team by the order the checks happen to run in.
    $user = User::factory()->create();
    $user->assignRole(RoleName::Manager->value);
    $user->givePermissionTo(PermissionName::AttendanceViewAll->value);

    expect($this->resolve->handle($user, 'attendance'))->toBe(ScopeLevel::All);
});

test('scope is resolved per resource family, not globally', function () {
    // Finance sees all attendance but, by the seeded matrix, has no leave
    // visibility beyond their own. One permission family must never widen another.
    $user = User::factory()->create();
    $user->assignRole(RoleName::Finance->value);

    expect($this->resolve->handle($user, 'attendance'))->toBe(ScopeLevel::All)
        ->and($this->resolve->handle($user, 'leave'))->toBe(ScopeLevel::Own);
});

test('an employee has no salary visibility at all', function () {
    // ADR-0010 in effect: salary.view-own is not seeded to the employee role.
    $user = User::factory()->create();
    $user->assignRole(RoleName::Employee->value);

    expect($this->resolve->handle($user, 'salary'))->toBe(ScopeLevel::None);
});

test('super admin sees every row of every resource', function () {
    $user = User::factory()->create();
    $user->assignRole(RoleName::SuperAdmin->value);

    foreach (['attendance', 'leave', 'salary'] as $family) {
        expect($this->resolve->handle($user, $family))->toBe(ScopeLevel::All);
    }
});

test('scope levels order from none to all', function () {
    expect(ScopeLevel::All->isAtLeast(ScopeLevel::Team))->toBeTrue()
        ->and(ScopeLevel::Own->isAtLeast(ScopeLevel::Team))->toBeFalse()
        ->and(ScopeLevel::None->seesAnything())->toBeFalse()
        ->and(ScopeLevel::Own->seesAnything())->toBeTrue();
});
