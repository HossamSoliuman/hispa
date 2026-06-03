<?php

namespace App\Http\Middleware;

use App\Traits\RespondsWithHttpStatus;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CaptainRole
{
    use RespondsWithHttpStatus;

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if (! $user || $user->role !== 'captain') {

            return $this->failure('Unauthorized. Only captains can access this resource.', [], 403);

        }

        return $next($request);
    }
}
