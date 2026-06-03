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
        if (empty($guards)) {
            $guards = ['owner', 'dalal', 'gov'];
        }

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $target = match ($guard) {
                    'owner' => route('owner.dashboard'),
                    'dalal' => route('dalal.dashboard'),
                    //                    'counter' => route('counter.dashboard'),
                    'gov' => route('gov.dashboard'),
                    default => route('landing-page'),
                };

                return redirect()->intended($target);
            }
        }

        return $next($request);
    }
}
