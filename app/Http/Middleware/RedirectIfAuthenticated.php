<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Middleware\RedirectIfAuthenticated as MiddlewareRedirectIfAuthenticated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated extends MiddlewareRedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        // The frontend login only ever authenticates the storefront guards. The
        // gov and admin panels have their own separate `guest:gov` / `guest:admin`
        // logins, so a session on one of those panels must NOT be treated as
        // "already authenticated" here — otherwise it blocks an owner from
        // logging in through the frontend (shared session cookie conflict).
        if (empty($guards)) {
            $guards = ['web', 'owner', 'dalal'];
        }

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $target = $guard === 'owner'
                    ? route('owner.dashboard')
                    : route('landing-page');

                return redirect()->intended($target);
            }
        }

        return $next($request);
    }
}
