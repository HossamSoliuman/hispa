<?php

use App\Http\Controllers\Gov\Auth\LoginController;
use App\Http\Controllers\Gov\ComingSoonController;
use App\Http\Controllers\Gov\DashboardController;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

Route::group([
    'prefix' => LaravelLocalization::setLocale(),
    'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath'],
], function () {
    Route::prefix('gov')->name('gov.')->group(function () {
        // Authentication (outside the auth middleware)
        Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [LoginController::class, 'login'])->name('login.submit');

        // Protected government supervisor routes
        Route::middleware(['auth:gov', 'gov'])->group(function () {
            Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

            // Dashboard (built)
            Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
            Route::get('/dashboard', [DashboardController::class, 'index']);

            // Every other section is a placeholder for now — all resolve to the
            // shared "coming soon" screen, titled from their route name.
            Route::get('/analytics', [ComingSoonController::class, 'show'])->name('analytics');
            Route::get('/production', [ComingSoonController::class, 'show'])->name('production');
            Route::get('/seasons', [ComingSoonController::class, 'show'])->name('seasons');
            Route::get('/fish-types', [ComingSoonController::class, 'show'])->name('fish_types');
            Route::get('/workforce', [ComingSoonController::class, 'show'])->name('workforce');
            Route::get('/fishing-tools', [ComingSoonController::class, 'show'])->name('fishing_tools');
            Route::get('/fishing-equipment', [ComingSoonController::class, 'show'])->name('fishing_equipment');
            Route::get('/reports', [ComingSoonController::class, 'show'])->name('reports');
            Route::get('/violations', [ComingSoonController::class, 'show'])->name('violations');
            Route::get('/ports', [ComingSoonController::class, 'show'])->name('ports');
            Route::get('/employees', [ComingSoonController::class, 'show'])->name('employees');
            Route::get('/roles', [ComingSoonController::class, 'show'])->name('roles');
            Route::get('/profile', [ComingSoonController::class, 'show'])->name('profile');
        });
    });
});
