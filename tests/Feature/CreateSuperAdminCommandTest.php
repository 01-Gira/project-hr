<?php

use App\Enums\AuditAction;
use App\Enums\RoleName;
use App\Models\AuditLog;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    $this->seed([PermissionSeeder::class, RoleSeeder::class]);
});

test('it creates a super admin account', function () {
    config(['absensi.super_admin_password' => 'Sup3r-Str0ng-Passw0rd']);

    $this->artisan('absensi:create-super-admin', [
        '--email' => 'owner@example.test',
        '--name' => 'Owner',
    ])->assertSuccessful();

    $user = User::where('email', 'owner@example.test')->firstOrFail();

    expect($user->hasRole(RoleName::SuperAdmin->value))->toBeTrue()
        ->and($user->uuid)->not->toBeNull();
});

test('the created super admin holds every permission', function () {
    config(['absensi.super_admin_password' => 'Sup3r-Str0ng-Passw0rd']);

    $this->artisan('absensi:create-super-admin', [
        '--email' => 'owner@example.test',
        '--name' => 'Owner',
    ])->assertSuccessful();

    $user = User::where('email', 'owner@example.test')->firstOrFail();

    expect($user->can('salary.view-all'))->toBeTrue()
        ->and($user->can('settings.update'))->toBeTrue()
        ->and($user->can('audit.view'))->toBeTrue();
});

test('it promotes an existing user without touching their password', function () {
    $existing = User::factory()->create([
        'email' => 'existing@example.test',
        'password' => 'Original-Passw0rd!',
    ]);
    $originalHash = $existing->password;

    $this->artisan('absensi:create-super-admin', ['--email' => 'existing@example.test'])
        ->assertSuccessful();

    $existing->refresh();

    expect($existing->hasRole(RoleName::SuperAdmin->value))->toBeTrue()
        ->and($existing->password)->toBe($originalHash)
        ->and(Hash::check('Original-Passw0rd!', $existing->password))->toBeTrue();
});

test('it is idempotent', function () {
    config(['absensi.super_admin_password' => 'Sup3r-Str0ng-Passw0rd']);

    $args = ['--email' => 'owner@example.test', '--name' => 'Owner'];

    $this->artisan('absensi:create-super-admin', $args)->assertSuccessful();
    $this->artisan('absensi:create-super-admin', $args)->assertSuccessful();

    expect(User::where('email', 'owner@example.test')->count())->toBe(1);
});

test('the bootstrap is audited', function () {
    config(['absensi.super_admin_password' => 'Sup3r-Str0ng-Passw0rd']);

    $this->artisan('absensi:create-super-admin', [
        '--email' => 'owner@example.test',
        '--name' => 'Owner',
    ])->assertSuccessful();

    $log = AuditLog::latest('id')->firstOrFail();

    expect($log->action)->toBe(AuditAction::RolePermissionChanged)
        ->and($log->new_values)->toBe(['roles' => [RoleName::SuperAdmin->value]])
        // No authenticated user during a console command: recorded as a system
        // action rather than attributed to someone who was not there.
        ->and($log->actor_user_id)->toBeNull();
});

test('it rejects a weak password', function () {
    config(['absensi.super_admin_password' => 'short']);

    $this->artisan('absensi:create-super-admin', [
        '--email' => 'owner@example.test',
        '--name' => 'Owner',
    ])->assertFailed();

    expect(User::where('email', 'owner@example.test')->exists())->toBeFalse();
});

test('it rejects an invalid email', function () {
    $this->artisan('absensi:create-super-admin', ['--email' => 'not-an-email'])
        ->assertFailed();
});
