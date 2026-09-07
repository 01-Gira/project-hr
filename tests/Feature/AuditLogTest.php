<?php

use App\Actions\Audit\RecordAuditLog;
use App\Enums\AuditAction;
use App\Http\Middleware\AssignRequestId;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Facades\Schema;

/*
| The audit foundation required by PHASE 0 (03_ROADMAP.md) and
| 05_IMPLEMENTATION_RULES.md §11. The roadmap's exit criterion is that an admin
| action can be recorded, so that is asserted directly rather than inferred from
| the presence of the table.
*/

beforeEach(function () {
    Context::flush();
});

test('an admin action is recorded with actor, subject and before/after values', function () {
    $actor = User::factory()->create();
    $subject = User::factory()->create();

    $log = app(RecordAuditLog::class)->handle(
        action: AuditAction::RolePermissionChanged,
        subject: $subject,
        actor: $actor,
        oldValues: ['status' => 'ACTIVE'],
        newValues: ['status' => 'SUSPENDED'],
        reason: 'Offboarding',
    );

    expect($log->action)->toBe(AuditAction::RolePermissionChanged)
        ->and($log->actor_user_id)->toBe($actor->id)
        ->and($log->subject_id)->toBe($subject->id)
        ->and($log->subject_type)->toBe($subject->getMorphClass())
        ->and($log->reason)->toBe('Offboarding')
        ->and($log->uuid)->not->toBeNull();
});

test('jsonb values round-trip as arrays', function () {
    $log = app(RecordAuditLog::class)->handle(
        action: AuditAction::SettingChanged,
        subject: User::factory()->create(),
        oldValues: ['nested' => ['a' => 1]],
        newValues: ['nested' => ['a' => 2]],
    );

    $fresh = $log->fresh();

    expect($fresh->old_values)->toBe(['nested' => ['a' => 1]])
        ->and($fresh->new_values)->toBe(['nested' => ['a' => 2]]);
});

test('the actor defaults to the authenticated user', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $log = app(RecordAuditLog::class)->handle(
        action: AuditAction::SettingChanged,
        subject: $user,
    );

    expect($log->actor_user_id)->toBe($user->id);
});

test('a system action is recorded with no actor rather than a fabricated one', function () {
    $log = app(RecordAuditLog::class)->handle(
        action: AuditAction::SettingChanged,
        subject: User::factory()->create(),
    );

    expect($log->actor_user_id)->toBeNull();
});

test('request evidence is captured from the context', function () {
    Context::add('audit.request_id', 'req-123');
    Context::addHidden('audit.ip_address', '203.0.113.7');
    Context::addHidden('audit.user_agent', 'ProbeAgent/1.0');

    $log = app(RecordAuditLog::class)->handle(
        action: AuditAction::SettingChanged,
        subject: User::factory()->create(),
    );

    expect($log->request_id)->toBe('req-123')
        ->and($log->ip_address)->toBe('203.0.113.7')
        ->and($log->user_agent)->toBe('ProbeAgent/1.0');
});

test('audit logs cannot be modified', function () {
    $log = app(RecordAuditLog::class)->handle(
        action: AuditAction::SettingChanged,
        subject: User::factory()->create(),
    );

    expect(fn () => $log->update(['reason' => 'rewritten']))
        ->toThrow(RuntimeException::class, 'append-only');
});

test('audit logs cannot be deleted', function () {
    $log = app(RecordAuditLog::class)->handle(
        action: AuditAction::SettingChanged,
        subject: User::factory()->create(),
    );

    expect(fn () => $log->delete())
        ->toThrow(RuntimeException::class, 'append-only');
});

test('the audit table has no updated_at column', function () {
    // A mutable audit trail is not an audit trail; the absence is deliberate.
    expect(Schema::hasColumn('audit_logs', 'updated_at'))->toBeFalse()
        ->and(Schema::hasColumn('audit_logs', 'created_at'))->toBeTrue();
});

test('deleting an actor preserves the record of what they did', function () {
    $actor = User::factory()->create();

    $log = app(RecordAuditLog::class)->handle(
        action: AuditAction::EmployeeUpdated,
        subject: User::factory()->create(),
        actor: $actor,
    );

    $actor->delete();

    $fresh = $log->fresh();

    expect($fresh)->not->toBeNull()
        ->and($fresh->actor_user_id)->toBeNull();
});

test('the middleware assigns a request id and hides the client evidence', function () {
    $request = Request::create('/', 'GET', server: [
        'REMOTE_ADDR' => '203.0.113.7',
        'HTTP_USER_AGENT' => 'ProbeAgent/1.0',
    ]);

    (new AssignRequestId)->handle($request, fn () => new Response);

    expect(Context::get('audit.request_id'))->not->toBeNull()
        ->and(Context::getHidden('audit.ip_address'))->toBe('203.0.113.7')
        ->and(Context::getHidden('audit.user_agent'))->toBe('ProbeAgent/1.0')
        // Hidden context stays out of log entries but still reaches queued jobs.
        ->and(Context::get('audit.ip_address'))->toBeNull();
});
