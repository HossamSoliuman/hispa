<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckOwnerAndActive
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        $user = auth()->user();

        if (! $user || $user->role !== 'owner' || $user->status != 1) {
            abort(403, 'غير مصرح لك بالوصول.');
        }

        return $next($request);
    }
}
