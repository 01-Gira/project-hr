<?php

namespace App\Actions\Audit;

use App\Enums\AuditAction;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Context;

/**
 * Writes one entry to the audit trail.
 *
 * Request evidence is read from the Context rather than from the request, so the
 * same action works identically inside an HTTP request, a queued job and an
 * Artisan command. AssignRequestId populates it for web traffic; elsewhere the
 * fields are simply null, which is honest rather than fabricated.
 */
class RecordAuditLog
{
    /**
     * @param  array<string, mixed>|null  $oldValues
     * @param  array<string, mixed>|null  $newValues
     */
    public function handle(
        AuditAction $action,
        Model $subject,
        ?User $actor = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?string $reason = null,
    ): AuditLog {
        return AuditLog::create([
            'actor_user_id' => ($actor ?? Auth::user())?->getKey(),
            'action' => $action,
            'subject_type' => $subject->getMorphClass(),
            'subject_id' => $subject->getKey(),
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'reason' => $reason,
            'ip_address' => Context::getHidden('audit.ip_address'),
            'user_agent' => Context::getHidden('audit.user_agent'),
            'request_id' => Context::get('audit.request_id'),
        ]);
    }
}
