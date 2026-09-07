<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

/**
 * Attaches request-scoped evidence to the Context for the audit trail.
 *
 * 02_DATABASE_DESIGN.md §17 requires audit entries to carry the actor's IP,
 * user agent and a request identifier. Passing those down through every service
 * signature would couple the whole domain layer to HTTP, so they travel in the
 * Context instead, which also propagates them into queued jobs.
 *
 * request_id is visible context, so it is attached to log entries and a log line
 * can be correlated with an audit row. IP and user agent are hidden: they still
 * reach queued jobs, but do not leak into every log line.
 */
class AssignRequestId
{
    public function handle(Request $request, Closure $next): Response
    {
        Context::add('audit.request_id', (string) Str::uuid7());
        Context::addHidden('audit.ip_address', $request->ip());
        Context::addHidden('audit.user_agent', $request->userAgent());

        return $next($request);
    }
}
