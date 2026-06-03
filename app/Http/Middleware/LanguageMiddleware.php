<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LanguageMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check header request and determine localizaton
        $local = ($request->hasHeader('x-localization')) ? $request->header('x-localization') : 'en';
        // set laravel localization
        app()->setLocale($local);

        // continue request
        return $next($request);
    }
}
