<?php

use App\Http\Controllers\Frontend\Auth\LoginController;
use App\Http\Controllers\Frontend\Auth\RegisterController;
use App\Http\Controllers\Frontend\CheckoutController;
use App\Http\Controllers\Frontend\ContactController;
use App\Http\Controllers\Frontend\LandingPageController;
use App\Http\Controllers\Frontend\PageController;
use App\Http\Controllers\SupportTicketController;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

Route::group([
    'prefix' => LaravelLocalization::setLocale(),
    'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath'],
], function () {
    Route::get('/', [LandingPageController::class, 'index'])->name('landing-page');
    Route::get('/about', [LandingPageController::class, 'about'])->name('site.about');
    Route::get('/pricing', [LandingPageController::class, 'pricing'])->name('site.pricing');
    Route::get('/contact', [LandingPageController::class, 'contact'])->name('site.contact');
    Route::get('/checkout', [CheckoutController::class, 'show'])->name('site.checkout');
    Route::post('/checkout/account', [CheckoutController::class, 'register'])->name('site.checkout.register');
    Route::get('/order-review', [CheckoutController::class, 'show'])->name('site.order-review');
    Route::get('/processing', [CheckoutController::class, 'show'])->name('site.processing');

    Route::middleware(['auth:owner', 'role:owner'])->group(function () {
        Route::post('/checkout/receipt', [CheckoutController::class, 'payment'])->name('site.checkout.payment');
        Route::get('/payment', [CheckoutController::class, 'show'])->name('site.payment');
        Route::post('/payment', [CheckoutController::class, 'payment'])->name('site.payment.store');
    });
    Route::get('/coming_soon', [LandingPageController::class, 'comingSoon'])->name('coming-soon');
    Route::post('/contact', [ContactController::class, 'store'])->name('frontend-contact.store');
    Route::get('/page/{slug}', [PageController::class, 'index'])->name('frontend.page');
    Route::get('/roles', [LandingPageController::class, 'roles'])->name('frontend.roles');

    // Support Ticket API Integration Routes
    Route::get('/support/priorities', [SupportTicketController::class, 'getPriorities'])->name('support.priorities');
    Route::get('/support/categories', [SupportTicketController::class, 'getCategories'])->name('support.categories');
    Route::post('/support/ticket', [SupportTicketController::class, 'createTicket'])->name('support.ticket.create');

    // Ifesh Marketplace (Public)
    Route::get('/ifesh-market', [App\Http\Controllers\IfeshMarketplaceController::class, 'index'])->name('ifesh.marketplace');
    Route::get('/ifesh-market/{id}', [App\Http\Controllers\IfeshMarketplaceController::class, 'show'])->name('ifesh.marketplace.show');

    Route::name('frontend.')->group(function () {
        //  start Authentication Routes...

        Route::middleware('guest')->group(function () {
            Route::get('/login', [LoginController::class, 'showLoginForm'])->name('show_login_form');
            Route::post('/login', [LoginController::class, 'login'])->name('login');
            Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('show_register_form');
            Route::post('/register', [RegisterController::class, 'register'])->name('register');
        });

        Route::group(['middleware' => ['auth:web', 'role:owner']], function () {
            Route::post('logout', [LoginController::class, 'logout'])->name('logout');
        });
    });
});
