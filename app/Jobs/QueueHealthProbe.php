<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Cache;

/**
 * Proves that a job survives serialisation, transport through Redis, and
 * execution by a worker.
 *
 * PHASE 0 exit criteria (03_ROADMAP.md) require a working queue, and Phase 9
 * requires queued Telegram delivery whose failure must not fail attendance
 * recording (05_IMPLEMENTATION_RULES.md §8). That guarantee only holds if the
 * queue is real infrastructure rather than a configuration value, so this job
 * exists to be asserted against in QueueConnectionTest.
 */
class QueueHealthProbe implements ShouldQueue
{
    use Queueable;

    public function __construct(public string $token) {}

    public function handle(): void
    {
        Cache::store('redis')->put("queue-probe:{$this->token}", 'ok', 60);
    }
}
