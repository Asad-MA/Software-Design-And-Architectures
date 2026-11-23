<?php

declare(strict_types=1);

namespace App\Core;

use Closure;

/**
 * Middleware contract. Call $next($request) to continue the pipeline,
 * or return a Response early to short-circuit (e.g. redirect guests).
 */
interface Middleware
{
    public function handle(Request $request, Closure $next): Response;
}
