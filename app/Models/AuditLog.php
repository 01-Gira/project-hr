<?php

namespace App\Models;

use App\Concerns\HasUuid;
use App\Enums\AuditAction;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;
use RuntimeException;

/**
 * @property int $id
 * @property string $uuid
 * @property int|null $actor_user_id
 * @property AuditAction $action
 * @property string $subject_type
 * @property int $subject_id
 * @property array<string, mixed>|null $old_values
 * @property array<string, mixed>|null $new_values
 * @property string|null $reason
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property string|null $request_id
 * @property Carbon|null $created_at
 */
#[Fillable([
    'actor_user_id', 'action', 'subject_type', 'subject_id',
    'old_values', 'new_values', 'reason',
    'ip_address', 'user_agent', 'request_id',
])]
class AuditLog extends Model
{
    use HasUuid;

    /**
     * Append-only: the table has no updated_at column.
     */
    const UPDATED_AT = null;

    /**
     * Enforce immutability at the model layer.
     *
     * The migration omits updated_at, but nothing in the schema stops an
     * UPDATE or DELETE. An audit trail that can be quietly rewritten provides
     * no accountability, so both are refused outright rather than discouraged
     * by convention.
     */
    protected static function booted(): void
    {
        static::updating(function (): never {
            throw new RuntimeException('Audit logs are append-only and cannot be modified.');
        });

        static::deleting(function (): never {
            throw new RuntimeException('Audit logs are append-only and cannot be deleted.');
        });
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'action' => AuditAction::class,
            'created_at' => 'datetime',
            'new_values' => 'array',
            'old_values' => 'array',
        ];
    }

    /**
     * The user who performed the action, if it was not a system action.
     *
     * @return BelongsTo<User, $this>
     */
    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_user_id');
    }

    /**
     * The record that was acted upon.
     *
     * @return MorphTo<Model, $this>
     */
    public function subject(): MorphTo
    {
        return $this->morphTo();
    }
}
