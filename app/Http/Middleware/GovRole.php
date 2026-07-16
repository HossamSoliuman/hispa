<?php

namespace App\Http\Middleware;

use App\Traits\RespondsWithHttpStatus;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class GovRole
{
    use RespondsWithHttpStatus;

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth('gov')->user();

        if (! $user || $user->role !== 'gov') {
            return $this->failure('Unauthorized. Only government supervisors can access this resource.', [], 403);
        }

        return $next($request);
    }
}
