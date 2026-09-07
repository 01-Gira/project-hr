<?php

namespace App\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Gives a model a public UUID identifier alongside its bigint primary key.
 *
 * ADR-0008: bigint primary keys stay internal because the high-volume tables
 * (attendance_events, audit_logs, telegram_delivery_logs) pay 8 bytes rather
 * than 16 in every row and every index. The public identifier is UUID v7, which
 * is time-sortable — it keeps the insert locality that motivates ULID while
 * remaining a native PostgreSQL uuid type.
 *
 * Do not expose the bigint id in URLs, API payloads or QR tokens.
 */
trait HasUuid
{
    protected static function bootHasUuid(): void
    {
        static::creating(function (Model $model): void {
            if (blank($model->getAttribute('uuid'))) {
                $model->setAttribute('uuid', (string) Str::uuid7());
            }
        });
    }

    /**
     * Resolve route model bindings on the public identifier, never the bigint id.
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }
}
