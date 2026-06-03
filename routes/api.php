<?php

use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\Auth\ForgetPasswordController;
use App\Http\Controllers\Api\V1\Auth\VerificationPhoneController;
use App\Http\Controllers\Api\V1\Captain\FishStockController;
use App\Http\Controllers\Api\V1\ContactController;
use App\Http\Controllers\Api\V1\CustomerController;
use App\Http\Controllers\Api\V1\Dalal\DalalSaleController;
use App\Http\Controllers\Api\V1\Dalal\DalalStockController;
use App\Http\Controllers\Api\V1\FishController;
use App\Http\Controllers\Api\V1\FishDefaultPriceController;
use App\Http\Controllers\Api\V1\LocationController;
use App\Http\Controllers\Api\V1\NotificationController;
use App\Http\Controllers\Api\V1\Owner\FishDalalStockController;
use App\Http\Controllers\Api\V1\Owner\SaleController;
use App\Http\Controllers\Api\V1\PageController;
use App\Http\Controllers\Api\V1\PaymentMethods;
use App\Http\Controllers\Api\V1\ProfileController;
use App\Http\Controllers\Api\V1\CouponController;
use App\Http\Controllers\Api\V1\SettingController;
use App\Http\Controllers\Api\V1\TripController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::group(['prefix' => 'v1', 'middleware' => 'lang'], function () {

    // login
    Route::post('login', [AuthController::class, 'login'])->name('user.login');

    // forget_pass
    Route::post('forget-password', [ForgetPasswordController::class, 'forgetPassword'])->name('user.forgetPassword');
    Route::post('check-code', [ForgetPasswordController::class, 'checkCode'])->name('user.checkCode');

    Route::post('reset-password', [ForgetPasswordController::class, 'resetPassword'])->name('user.resetPassword');

    // send code & verify
    Route::post('send-code', [VerificationPhoneController::class, 'createCode'])->name('user.createCode');
    Route::post('verfiy-code', [VerificationPhoneController::class, 'verify'])->name('user.verfiyCode');

    Route::group(['middleware' => 'auth:sanctum'], function () {

        // notifications
        Route::get('/get-notification-all', [NotificationController::class, 'getNotification'])->name('get-notification');
        Route::post('/read-notification', [NotificationController::class, 'readNotification'])->name('read-notification');

        // logout
        Route::post('logout', [AuthController::class, 'logout'])->name('user.logout');
        // profile
        Route::get('profile', [ProfileController::class, 'profile'])->name('user.profile');
        Route::post('update-profile', [ProfileController::class, 'updateProfile'])->name('user.update-profile');
        // Regions
        Route::get('regions', [LocationController::class, 'getRegions'])->name('user.regions');
        // Governorates
        Route::get('governorates/{region_id}', [LocationController::class, 'getGovernorates'])->name('user.governorates');
        // Cities
        Route::get('cities/{governorate_id}', [LocationController::class, 'getCities'])->name('user.cities');
        // Ports
        Route::get('ports/{city_id}', [LocationController::class, 'getPorts'])->name('user.ports');

        // locations
        Route::get('locations', [LocationController::class, 'getLocation'])->name('user.locations');

        // update-password
        Route::post('update-password', [ProfileController::class, 'updatePassword'])->name('user.update-password');
        // contact
        Route::post('create-contact', [ContactController::class, 'createContact'])->name('user.createContact');

        // trips
        Route::apiResource('trips', TripController::class);

        // get Trips TripsAvailableCounter
        Route::get('trips-available-counter', [TripController::class, 'TripsAvailableCounter'])->name('user.trips-available-counter');

        Route::post('create-contact', [ContactController::class, 'createContact'])->name('user.createContact');

        // fish
        Route::get('fish', [FishController::class, 'index'])->name('user.fish.index');
        Route::get('fish/{id}', [FishController::class, 'show'])->name('user.fish.show');

        // fish Stock
        Route::apiResource('fish_stock', FishStockController::class);

        // payment methods
        Route::get('payment-methods', [PaymentMethods::class, 'index'])->name('user.payment.index');

        // Customers
        Route::apiResource('customers', CustomerController::class);

        // fish default price owner and dalal
        Route::apiResource('fish-default-price', FishDefaultPriceController::class);

        // owner to add items stock //owner

        //
        // Captain
        Route::middleware(['captain'])->group(function () {});

        Route::middleware(['owner'])->group(function () {
            // Sales -owner // owner
            Route::apiResource('sales', SaleController::class);
            Route::delete('saleDetails-delete/{sale_details_id}', [SaleController::class, 'destroySalesDetails'])->name('user.sales-invoice');
            // finish_sales  // owner
            Route::post('finish_sales', [SaleController::class, 'finish_sales'])->name('user.finish_sales');
            Route::get('get-dalals', [DalalStockController::class, 'getDalals'])->name('user.dalals');

            // fish dalal Stock
            Route::apiResource('fish-dalal-stock', FishDalalStockController::class);
            Route::delete('stock-detail-delete/{stock_detail}', [FishDalalStockController::class, 'destroyStockDetail'])->name('user.stock-detail-delete');

            Route::post('update-status-dalalStock', [FishDalalStockController::class, 'update_status_stock'])->name('user.update_status_stock');
        });
        // Dalal

        Route::middleware(['dalal'])->group(function () {
            Route::apiResource('dalal-stock', DalalStockController::class);
            Route::apiResource('dalal-sales', DalalSaleController::class);
            Route::delete('dalal-saleDetails-delete/{sale_details_id}', [DalalSaleController::class, 'destroySalesDetails'])->name('user.dalal-sales-invoice');
            Route::post('finish-dalal-sales', [DalalSaleController::class, 'finish_sales'])->name('user.finish_dalal_sales');
        });
    });

    // validate coupon (for subscription checkout - public)
    Route::post('validate-coupon', [CouponController::class, 'validate'])->name('coupon.validate');

    // general settings
    Route::get('settings', [SettingController::class, 'index'])->name('general.settings');
    // pages
    Route::get('pages', [PageController::class, 'pages'])->name('pages');
    Route::post('/sendNotificationFirebase', [NotificationController::class, 'sendNotificationFirebase'])->name('employee.NotificationFirebase');
});
