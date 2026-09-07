<?php

use App\Enums\RoleName;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;

beforeEach(function () {
    $this->seed([PermissionSeeder::class, RoleSeeder::class]);
});

test('guests are redirected to the login page', function () {
    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('login'));
});

test('authenticated users with dashboard permission can visit the dashboard', function () {
    $user = User::factory()->create();
    $user->assignRole(RoleName::Employee->value);
    $this->actingAs($user);

    $response = $this->get(route('dashboard'));
    $response->assertOk();
});

test('a user holding no role is denied the dashboard', function () {
    // Deny-by-default. dashboard.view is seeded to every role, so reaching this
    // branch means the account was granted nothing at all.
    $this->actingAs(User::factory()->create());

    $this->get(route('dashboard'))->assertForbidden();
});

test('every default role can reach the dashboard', function () {
    foreach (RoleName::cases() as $role) {
        $user = User::factory()->create();
        $user->assignRole($role->value);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk();
    }
});
