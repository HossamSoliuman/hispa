<?php

use App\Http\Middleware\Authenticate;
use App\Http\Middleware\CaptainRole;
use App\Http\Middleware\CheckOwnerAndActive;
use App\Http\Middleware\DalalRole;
use App\Http\Middleware\LanguageMiddleware;
use App\Http\Middleware\OwnerRole;
use App\Http\Middleware\RedirectIfAuthenticated;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
        then: function () {
            Route::middleware('web')
                ->group(base_path('routes/owner.php'));
            Route::middleware('web')
                ->group(base_path('routes/admin.php'));
        }
    )
    ->withMiddleware(function (Middleware $middleware): void {

        // The owner theme preference is set client-side via JS (plaintext cookie),
        // so it must be excluded from cookie encryption to be readable server-side.
        $middleware->encryptCookies(except: ['owner_theme']);

        $middleware->alias([
            'localize' => \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRoutes::class,
            'localizationRedirect' => \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter::class,
            'localeSessionRedirect' => \Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect::class,
            'localeCookieRedirect' => \Mcamara\LaravelLocalization\Middleware\LocaleCookieRedirect::class,
            'localeViewPath' => \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationViewPath::class,
            'lang' => LanguageMiddleware::class,
            'captain' => CaptainRole::class,
            'owner' => OwnerRole::class,
            'dalal' => DalalRole::class,
            'guest' => RedirectIfAuthenticated::class,
            'check-owner-active' => CheckOwnerAndActive::class,
            'auth' => Authenticate::class,
            'role' => RoleMiddleware::class,
            'permission' => PermissionMiddleware::class,

        ]);
        // Configure guest redirects for admin
        $middleware->redirectGuestsTo(function ($request) {
            if ($request->is('admin/*')) {
                return route('admin.show_login_form');
            }
            if ($request->is('gov/*')) {
                return route('gov.login');
            }
            if ($request->is('owner/*')) {
                return route('frontend.login');
            }
            if ($request->is('dalal/*')) {
                return route('frontend.login');
            }

            return route('frontend.login');
        });

        // Configure authenticated user redirects
        $middleware->redirectUsersTo(function ($request) {
            if ($request->is('admin/*')) {
                return route('admin.dashboard');
            }
            if ($request->is('owner/*')) {
                return route('owner.dashboard');
            }
            if ($request->is('dalal/*')) {
                return route('frontend.dashboard.user');
            }
            if ($request->is('gov/*')) {
                return route('gov.dashboard');
            }

            return route('frontend.dashboard.user');
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
