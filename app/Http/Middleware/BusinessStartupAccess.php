<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BusinessStartupAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        abort_unless(auth('owner')->user()?->hasBusinessStartupAccess(), 404);

        return $next($request);
    }
}
