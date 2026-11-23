<?php

namespace App\Middleware;

use App\Core;
use App\Core\Middleware;
use App\Core\Request;
use App\Core\Response;
use Closure;

final class AuthMiddleware implements Middleware
{
    public function __construct(){

    }

    public function handle(Request $request, Closure $next): Response {



        return $next($request);
    }
}
