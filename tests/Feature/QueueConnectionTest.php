<?php

use App\Jobs\QueueHealthProbe;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;

/*
| These tests talk to the real Redis server from docker-compose.yml rather than
| faking it. Configuration is not execution: `config('queue.default') === 'redis'`
| proves nothing about whether a job can actually be enqueued and run. Grouped as
| "infrastructure" so they can be excluded with
| `php artisan test --exclude-group=infrastructure` when Redis is unavailable.
|
| phpunit.xml sets QUEUE_CONNECTION=sync and CACHE_STORE=array for the suite, so
| the Redis connection and store are addressed explicitly here.
*/

test('a job is transported through redis and executed by a worker', function () {
    $token = Str::random(16);
    $key = "queue-probe:{$token}";
    $queueName = 'health-probe';

    $cache = Cache::store('redis');
    $cache->forget($key);

    $queue = Queue::connection('redis');
    $sizeBefore = $queue->size($queueName);

    $queue->pushOn($queueName, new QueueHealthProbe($token));

    // The job is on the wire, not merely dispatched into a fake.
    expect($queue->size($queueName))->toBe($sizeBefore + 1);
    expect($cache->get($key))->toBeNull();

    Artisan::call('queue:work', [
        'connection' => 'redis',
        '--queue' => $queueName,
        '--once' => true,
        '--stop-when-empty' => true,
    ]);

    expect($cache->get($key))->toBe('ok');
    expect($queue->size($queueName))->toBe($sizeBefore);

    $cache->forget($key);
})->group('infrastructure');

test('the redis cache store round-trips a value', function () {
    $key = 'cache-probe:'.Str::random(16);
    $cache = Cache::store('redis');

    $cache->put($key, 'ok', 60);

    expect($cache->get($key))->toBe('ok');

    $cache->forget($key);

    expect($cache->get($key))->toBeNull();
})->group('infrastructure');
