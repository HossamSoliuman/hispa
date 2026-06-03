<?php

namespace App\Http\Middleware;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Middleware\Authenticate as MiddlewareAuthenticate;
use Illuminate\Http\Request;

class Authenticate extends MiddlewareAuthenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    protected function unauthenticated($request, array $guards)
    {
        if ($request->expectsJson()) {
            throw new AuthenticationException('Unauthenticated.', $guards);
        }

        $guard = $guards[0] ?? null;

        $login = match ($guard) {
            'admin' => route('admin.show_login_form'),
            'owner' => route('frontend.login'),
            'dalal' => route('frontend.login'),
            'gov' => route('gov.login'),
            default => route('frontend.login'),
        };

        throw new AuthenticationException('Unauthenticated.', $guards, $login);
    }

    protected function redirectTo($request): ?string
    {
        if ($request->expectsJson()) {
            return null;
        }
        // Redirect to correct login based on path (path may include locale: ar/admin/sales)
        if ($request->is('admin/*') || $request->is('*/admin/*') || $request->is('*/admin')) {
            return route('admin.show_login_form');
        }
        if ($request->is('gov/*') || $request->is('*/gov/*') || $request->is('*/gov')) {
            return route('gov.login');
        }
        return route('frontend.login');
    }
}
