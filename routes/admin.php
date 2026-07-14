<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AdminProfileController;
use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\BoatController;
use App\Http\Controllers\Admin\BoatTypeController;
use App\Http\Controllers\Admin\CaptainController as AdminCaptainController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\CrewController as AdminCrewController;
use App\Http\Controllers\Admin\CustomFeatureController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FishController as AdminFishController;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\OwnerController;
use App\Http\Controllers\Admin\OwnerStockController;
use App\Http\Controllers\Admin\Report\BoatReportController;
use App\Http\Controllers\Admin\Report\FishHistoryReportController;
use App\Http\Controllers\Admin\Report\OwnerReportController;
use App\Http\Controllers\Admin\Report\ReportsHubController;
use App\Http\Controllers\Admin\Report\RevenueReportController;
use App\Http\Controllers\Admin\Report\SalesReportController;
use App\Http\Controllers\Admin\Report\StockReportController;
use App\Http\Controllers\Admin\Report\TripReportController;
use App\Http\Controllers\Admin\SalesController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\StockController;
use App\Http\Controllers\Admin\SubscriptionController;
use App\Http\Controllers\Admin\SubscriptionPackageController;
use App\Http\Controllers\Admin\TripController;
use App\Http\Controllers\Owner\CategoryController;
use App\Http\Controllers\Owner\CustomersController;
use App\Http\Controllers\Owner\GovernorateController;
use App\Http\Controllers\Owner\LocationController;
use App\Http\Controllers\Owner\PortController;
use App\Http\Controllers\Owner\RegionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

Route::group([
    'prefix' => LaravelLocalization::setLocale(),
    'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath'],
], function () {
    Route::prefix('admin')->name('admin.')->group(function () {
        // Authentication Routes (must be outside auth middleware)
        Route::get('/login', [LoginController::class, 'showLoginForm'])->name('show_login_form');
        Route::post('/login', [LoginController::class, 'login'])->name('login');

        // Protected Admin Routes
        Route::middleware(['auth:admin'])->group(function () {
            // Logout (must be inside auth middleware)
            Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

            // Dashboard
            Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
            Route::get('/dashboard', [DashboardController::class, 'index']);
            Route::get('/dashboard/statistics', [DashboardController::class, 'statistics'])->name('dashboard.statistics');

            // Trips (admin oversees + edits owner trips; trip creation lives in the owner panel)
            Route::resource('trips', TripController::class)->except(['create', 'store']);
            Route::get('getTripData', [TripController::class, 'getTripData'])->name('getTripData');

            // Boats
            Route::resource('boats', BoatController::class);
            Route::get('getBoatData', [BoatController::class, 'getBoatData'])->name('getBoatData');

            // Boat Types
            Route::resource('boat_types', BoatTypeController::class);
            Route::get('getBoatTypeData', [BoatTypeController::class, 'getBoatTypeData'])->name('getBoatTypeData');

            // Subscription Packages
            Route::resource('subscription-packages', SubscriptionPackageController::class);

            // Coupons
            Route::resource('coupons', CouponController::class);

            // Subscriptions
            Route::resource('subscriptions', SubscriptionController::class);
            Route::post('subscriptions/{subscription}/suspend', [SubscriptionController::class, 'suspend'])->name('subscriptions.suspend');
            Route::post('subscriptions/{subscription}/unsuspend', [SubscriptionController::class, 'unsuspend'])->name('subscriptions.unsuspend');
            Route::post('subscriptions/{subscription}/renew', [SubscriptionController::class, 'renew'])->name('subscriptions.renew');

            // Custom Features
            Route::get('custom-features', [CustomFeatureController::class, 'index'])->name('custom-features.index');
            Route::get('custom-features/{feature}', [CustomFeatureController::class, 'show'])->name('custom-features.show');
            Route::get('custom-features/{feature}/owners/search', [CustomFeatureController::class, 'searchOwners'])->name('custom-features.owners.search');
            Route::post('custom-features/{feature}/access', [CustomFeatureController::class, 'store'])->name('custom-features.access.store');
            Route::patch('custom-features/{feature}/access/{access}/pause', [CustomFeatureController::class, 'pause'])->name('custom-features.access.pause');
            Route::patch('custom-features/{feature}/access/{access}/resume', [CustomFeatureController::class, 'resume'])->name('custom-features.access.resume');
            Route::delete('custom-features/{feature}/access/{access}', [CustomFeatureController::class, 'destroy'])->name('custom-features.access.destroy');

            // Invoices (static paths must precede the resource so they aren't shadowed by invoices/{invoice})
            Route::get('invoices-export', [InvoiceController::class, 'export'])->name('invoices.export');
            Route::get('invoices/{invoice}/print', [InvoiceController::class, 'print'])->name('invoices.print');
            Route::post('invoices/{invoice}/confirm-payment', [InvoiceController::class, 'confirmPayment'])->name('invoices.confirm-payment');
            Route::resource('invoices', InvoiceController::class)->except(['create', 'store']);

            // Profile
            Route::get('profile', [AdminProfileController::class, 'index'])->name('profile.index');
            Route::put('profile/{id}', [AdminProfileController::class, 'update'])->name('profile.update');

            // Notifications
            Route::get('notifications', [NotificationController::class, 'index'])->name('notifications');
            Route::get('notifications/fetch', [NotificationController::class, 'fetch'])->name('notifications.fetch');
            Route::post('notifications/read-all', [NotificationController::class, 'readAll'])->name('notifications.readAll');
            Route::get('notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');

            // Support Ticket API Integration Routes
            Route::get('support/priorities', [\App\Http\Controllers\SupportTicketController::class, 'getPriorities'])->name('support.priorities');
            Route::get('support/categories', [\App\Http\Controllers\SupportTicketController::class, 'getCategories'])->name('support.categories');
            Route::get('support/tickets/phone', [\App\Http\Controllers\SupportTicketController::class, 'getUserTickets'])->name('support.tickets.user');
            Route::post('support/ticket', [\App\Http\Controllers\SupportTicketController::class, 'createTicket'])->name('support.ticket.create');

            // Fish
            Route::get('getFishData', [AdminFishController::class, 'getFishData'])->name('getFishData');
            Route::resource('fish', AdminFishController::class)->only(['index', 'store', 'update', 'destroy']);

            // Categories
            Route::resource('categories', CategoryController::class);
            Route::get('getCategoriesData', [CategoryController::class, 'getData'])->name('getCategoriesData');

            // Owners (Fishermen) - User model with role=owner
            Route::get('owner', [OwnerController::class, 'index'])->name('owner.index');
            Route::get('owner/create', [OwnerController::class, 'create'])->name('owner.create');
            Route::post('owner', [OwnerController::class, 'store'])->name('owner.store');
            Route::get('getOwnerData', [OwnerController::class, 'getOwnerData'])->name('getOwnerData');
            Route::get('owner/{id}/edit', [OwnerController::class, 'edit'])->name('owner.edit');
            Route::match(['put', 'patch'], 'owner/{id}', [OwnerController::class, 'update'])->name('owner.update');
            Route::get('owner/{id}', [OwnerController::class, 'show'])->name('owner.show');

            // Locations (for captain/crew forms)
            Route::get('getGovernorates/{region_id}', [LocationController::class, 'getGovernorates'])->name('getGovernorates');
            Route::get('getPorts/{gov_id}', [LocationController::class, 'getPorts'])->name('getPorts');

            // Captains (القائمة + التفاصيل + تعديل/حذف)
            Route::resource('captain', AdminCaptainController::class)->only(['index', 'show', 'edit', 'update', 'destroy']);
            Route::get('getCaptainData', [AdminCaptainController::class, 'getCaptainData'])->name('getCaptainData');

            // Crews (عرض فقط: القائمة + صفحة التفاصيل)
            Route::resource('crew', AdminCrewController::class)->only(['index', 'show']);
            Route::get('getCrewData', [AdminCrewController::class, 'getCrewData'])->name('getCrewData');

            // Customers
            Route::resource('customers', CustomersController::class);
            Route::get('getCustomerData', [CustomersController::class, 'getCustomerData'])->name('getCustomerData');

            // Admins
            Route::resource('admins', AdminController::class);

            // Stocks
            Route::get('stocks', [StockController::class, 'index'])->name('stocks.index');
            Route::get('stocks/{id}', [StockController::class, 'show'])->name('stocks.show');
            Route::get('getStockData', [StockController::class, 'getStockData'])->name('getStockData');
            Route::get('stocks/{id}/detail', [StockController::class, 'getShowDetailStockData'])->name('stocks.detail');

            // Owner Stock (fish quantity per owner – same format as owner/fish-quntity)
            Route::get('owner-stock', [OwnerStockController::class, 'index'])->name('owner-stock.index');
            Route::get('getOwnerStockData', [OwnerStockController::class, 'getOwnerStockData'])->name('getOwnerStockData');
            Route::get('owner-stock/{id}/detail-data', [OwnerStockController::class, 'getOwnerStockDetailData'])->name('owner-stock.detail-data');
            Route::get('owner-stock/{id}', [OwnerStockController::class, 'show'])->name('owner-stock.show');

            // Dalal Stock (using same controller)
            Route::get('dalal-stock', [StockController::class, 'index'])->name('dalal-stock.index');
            Route::get('dalal-stock/{id}', [StockController::class, 'show'])->name('dalal-stock.show');

            // Counter (using User model with role=counter)
            Route::get('counter', function (Request $request) {
                $data = \App\Models\User::where('role', 'counter')->orderBy('id', 'DESC')->get();

                return view('admin.counter.index', compact('data'))
                    ->with('i', ($request->input('page', 1) - 1) * 5);
            })->name('counter.index');
            Route::get('getCounterData', function (Request $request) {
                $datatable = new \App\DataTable\CoutnerDataTable;

                return $datatable->getData($request);
            })->name('getCounterData');

            // Counter edit route (placeholder - to be implemented)
            Route::get('counter/{id}/edit', function ($id) {
                return redirect()->route('admin.counter.index')
                    ->with('info', __('admin.actions.edit').' - Counter ID: '.$id);
            })->name('counter.edit');

            // Counter show route (placeholder - to be implemented)
            Route::get('counter/{id}', function ($id) {
                return redirect()->route('admin.counter.index')
                    ->with('info', __('admin.actions.view').' - Counter ID: '.$id);
            })->name('counter.show');

            // Sales
            Route::resource('sales', SalesController::class);
            Route::get('getSalesData', [SalesController::class, 'getSalesData'])->name('getSalesData');

            // Reports
            Route::get('sales-report', [SalesReportController::class, 'index'])->name('sales-report');
            Route::get('sales-report/print', [SalesReportController::class, 'print'])->name('sales-report.print');
            Route::get('getSalesDataReport', [SalesReportController::class, 'getSalesData'])->name('getSalesDataReport');

            Route::get('stock-report', [StockReportController::class, 'index'])->name('stock-report');
            Route::get('stock-report/print', [StockReportController::class, 'print'])->name('stock-report.print');
            Route::get('getStockDataReport', [StockReportController::class, 'getStockData'])->name('getStockDataReport');

            // Reports hub (landing page grouping every platform-wide report)
            Route::get('reports', [ReportsHubController::class, 'index'])->name('reports-hub');

            // Trip report (platform-wide)
            Route::get('trip-report', [TripReportController::class, 'index'])->name('trip-report');
            Route::get('trip-report/print/{trip_id?}', [TripReportController::class, 'print'])->name('trip-report.print');
            Route::get('getTripDataReport', [TripReportController::class, 'getTripData'])->name('getTripDataReport');

            // Fish stock history report (platform-wide)
            Route::get('fish-history-report', [FishHistoryReportController::class, 'index'])->name('fish-history-report');
            Route::get('fish-history-report/print', [FishHistoryReportController::class, 'print'])->name('fish-history-report.print');
            Route::get('getFishHistoryDataReport', [FishHistoryReportController::class, 'getFishHistoryData'])->name('getFishHistoryDataReport');

            // Boats report (platform-wide)
            Route::get('boat-report', [BoatReportController::class, 'index'])->name('boat-report');
            Route::get('boat-report/print', [BoatReportController::class, 'print'])->name('boat-report.print');

            // Owners overview report (platform-wide)
            Route::get('owner-report', [OwnerReportController::class, 'index'])->name('owner-report');
            Route::get('owner-report/print', [OwnerReportController::class, 'print'])->name('owner-report.print');

            // Subscriptions & revenue report (platform-wide)
            Route::get('revenue-report', [RevenueReportController::class, 'index'])->name('revenue-report');
            Route::get('revenue-report/print', [RevenueReportController::class, 'print'])->name('revenue-report.print');

            // Settings (tabbed: company, fish, categories, regions, governorates, ports)
            Route::post('settings/company', [SettingController::class, 'updateCompany'])->name('settings.company');
            Route::resource('settings', SettingController::class);
            Route::resource('regions', RegionController::class)->only(['store', 'update', 'destroy']);
            Route::resource('governorates', GovernorateController::class)->only(['store', 'update', 'destroy']);
            Route::resource('ports', PortController::class)->only(['store', 'update', 'destroy']);

        });
    });
});
