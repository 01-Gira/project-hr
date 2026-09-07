<?php

use App\Concerns\HasUuid;
use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Ramsey\Uuid\Uuid;

/*
| Covers the identifier and lifecycle columns 02_DATABASE_DESIGN.md §2 requires
| on users, and the decisions in ADR-0008 (bigint primary key, UUID v7 public
| identifier).
*/

test('a new user is assigned a uuid automatically', function () {
    $user = User::factory()->create();

    expect($user->uuid)->not->toBeNull();
});

test('the assigned uuid is version 7 so it stays time-sortable', function () {
    $user = User::factory()->create();

    // ADR-0008 chose v7 specifically. v4 would scatter B-tree inserts across the
    // index on the high-volume tables this trait is destined for, which is the
    // defect the choice exists to avoid.
    expect(Uuid::fromString($user->uuid)->getFields()->getVersion())->toBe(7);
});

test('uuids are unique across users', function () {
    $uuids = User::factory()->count(5)->create()->pluck('uuid');

    expect($uuids->unique())->toHaveCount(5);
});

test('an explicitly provided uuid is not overwritten', function () {
    $uuid = (string) Uuid::uuid7();

    $user = User::factory()->create(['uuid' => $uuid]);

    expect($user->uuid)->toBe($uuid);
});

test('a new user is active by default', function () {
    $user = User::factory()->create();

    expect($user->status)->toBe(UserStatus::Active)
        ->and($user->status->canAuthenticate())->toBeTrue();
});

test('only active users may authenticate', function () {
    expect(UserStatus::Inactive->canAuthenticate())->toBeFalse()
        ->and(UserStatus::Suspended->canAuthenticate())->toBeFalse();
});

test('last login is null until recorded', function () {
    expect(User::factory()->create()->last_login_at)->toBeNull();
});

test('route model binding resolves on the uuid, never the bigint id', function () {
    expect((new User)->getRouteKeyName())->toBe('uuid');
});

test('the users table carries the columns the blueprint requires', function () {
    expect(Schema::hasColumns('users', ['uuid', 'status', 'last_login_at']))->toBeTrue();
});

test('the uuid trait is applied to the user model', function () {
    expect(class_uses_recursive(User::class))->toContain(HasUuid::class);
});
