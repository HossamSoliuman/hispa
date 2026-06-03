<?php

use App\Http\Controllers\Frontend\Auth\LoginController;
use App\Http\Controllers\Owner\AnalyticsController;
use App\Http\Controllers\Owner\AssetController;
use App\Http\Controllers\Owner\BoatController;
use App\Http\Controllers\Owner\CaptainController;
use App\Http\Controllers\Owner\CatchController;
use App\Http\Controllers\Owner\CategoryController;
use App\Http\Controllers\Owner\CityController;
use App\Http\Controllers\Owner\CrewCheckController;
use App\Http\Controllers\Owner\CrewController;
use App\Http\Controllers\Owner\CustomersController;
use App\Http\Controllers\Owner\DailyReportController;
use App\Http\Controllers\Owner\DalalPaymentController;
use App\Http\Controllers\Owner\DalalSalesController;
use App\Http\Controllers\Owner\DalalStockController;
use App\Http\Controllers\Owner\DashboardController;
use App\Http\Controllers\Owner\DataEntryController;
use App\Http\Controllers\Owner\DocumentsController;
use App\Http\Controllers\Owner\EmployeeController;
use App\Http\Controllers\Owner\ExpenseablesController;
use App\Http\Controllers\Owner\ExpensesController;
use App\Http\Controllers\Owner\FinancialReportsController;
use App\Http\Controllers\Owner\FishController;
use App\Http\Controllers\Owner\FishingEquipmentController;
use App\Http\Controllers\Owner\GovernorateController;
use App\Http\Controllers\Owner\InspectionsController;
use App\Http\Controllers\Owner\LocationController;
use App\Http\Controllers\Owner\MaintenanceController;
use App\Http\Controllers\Owner\NotificationController;
use App\Http\Controllers\Owner\PayrollController;
use App\Http\Controllers\Owner\PortController;
use App\Http\Controllers\Owner\ProfileController;
use App\Http\Controllers\Owner\ProfitLossController;
use App\Http\Controllers\Owner\RegionController;
use App\Http\Controllers\Owner\Report\DalalStockReportController;
use App\Http\Controllers\Owner\Report\FishHistoryReportController;
use App\Http\Controllers\Owner\Report\SalesReportController;
use App\Http\Controllers\Owner\Report\StockReportController;
use App\Http\Controllers\Owner\Report\TripReportController;
use App\Http\Controllers\Owner\ReportsController;
use App\Http\Controllers\Owner\ReturnsController;
use App\Http\Controllers\Owner\SalesController;
use App\Http\Controllers\Owner\SettingsController;
use App\Http\Controllers\Owner\StockController;
use App\Http\Controllers\Owner\TripController;
use App\Http\Controllers\Owner\UserRequestController;
use App\Http\Controllers\Owner\VendorsController;
use App\Http\Controllers\SupportTicketController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

Route::group([
    'prefix' => LaravelLocalization::setLocale(),
    'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath'],
], function () {
    Route::group([
        'prefix' => 'owner',
        'as' => 'owner.',
        'middleware' => ['auth:owner', 'role:owner'],
    ], function () {

        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard', [DashboardController::class, 'index']);
        // dashbord statticstis
        Route::get('/dashboard/overview-data', [DashboardController::class, 'overviewData'])->name('overview.data');
        Route::get('/recent-activities', [DashboardController::class, 'getRecentActivities'])
            ->name('recent.activities');
        Route::get('/financial-summary', [DashboardController::class, 'summary'])->name('financial.summary');
        Route::get('/operations/data', [DashboardController::class, 'getOperationsData'])->name('operations.data');
        Route::get('/analytics-data', [DashboardController::class, 'getAnalyticsData'])->name('analytics.data');
        // users_Requests
        Route::resource('/user_request', UserRequestController::class);

        Route::post('logout', [LoginController::class, 'logout'])->name('logout');

        // profile
        Route::resource('/profile', ProfileController::class);
        // get  governorates & cities with ajax
        Route::get('/get-governorates/{region_id}', [LocationController::class, 'getGovernorates'])->name('getGovernorates');
        Route::get('/get-ports/{gov_id}', [LocationController::class, 'getPorts'])->name('getPorts');

        // Route::get('/get-ports/{city_id}', [LocationController::class, 'getPorts'])->name('getPorts');
        Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications');
        Route::get('/notifications/fetch-users', [NotificationController::class, 'fetchUsers']);
        Route::get('/getNotificationData', [NotificationController::class, 'getNotificationData'])->name('getNotificationData');

        Route::post('/send_notifications', [NotificationController::class, 'sendNotifications'])->name('notifications.send');

        Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications');
        Route::post('/notifications/read-all', [NotificationController::class, 'readAll'])->name('notifications.readAll');
        Route::get('/notifications/fetch', [NotificationController::class, 'fetch'])->name('notifications.fetch');
        Route::post('/notifications/read-all', [NotificationController::class, 'readAll'])->name('notifications.readAll');
        Route::get('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
        Route::post('/notifications/read/{id}', function ($id) {
            /** @var \App\Models\Owner|null $owner */
            $owner = Auth::guard('owner')->user();
            abort_unless($owner, 403);

            $notification = $owner->notifications()->findOrFail($id);
            $notification->markAsRead();

            return response()->json(['message' => 'read']);
        });
        Route::get('/notifications/unread-count', function () {
            /** @var \App\Models\Owner|null $owner */
            $owner = Auth::guard('owner')->user();
            $count = $owner ? $owner->unreadNotifications()->count() : 0;

            return response()->json([
                'count' => $count,
            ]);
        });
        // trips
        Route::resource('/trips', TripController::class);
        Route::get('/getTripData', [TripController::class, 'getTripData'])->name('getTripData');
        Route::post('/endTrip/{id}', [TripController::class, 'endTrip'])->name('endTrip');
        Route::post('/cancelTrip/{id}', [TripController::class, 'cancelTrip'])->name('cancelTrip');

        // boats
        Route::resource('/boats', BoatController::class);
        Route::get('/getBoatData', [BoatController::class, 'getBoatData'])->name('getBoatData');
        Route::get('/getBoatInfo/{id}', [BoatController::class, 'getBoatInfo'])->name('getBoatInfo');
        Route::get('/getBoatInfoByTrip/{id}', [BoatController::class, 'getBoatInfoByTrip'])->name('getBoatInfoByTrip');

        Route::resource('/assets', AssetController::class);
        Route::get('/getAssetsData', [AssetController::class, 'getAssetsData'])->name('getAssetsData');

        Route::resource('/returns', ReturnsController::class);
        Route::get('/getReturnsData', [ReturnsController::class, 'getReturnsData'])->name('getReturnsData');
        Route::get('/saleDetails/{id}', [ReturnsController::class, 'saleDetails'])->name('saleDetails');

        Route::resource('/categories', CategoryController::class);
        Route::get('/getCategoriesData', [CategoryController::class, 'getData'])->name('getCategoriesData');

        Route::resource('/fish', FishController::class);
        Route::get('/getFishData', [FishController::class, 'getFishData'])->name('getFishData');
        Route::get('/fishStock', [FishController::class, 'getFishStock'])->name('fishStock');

        // captains
        Route::resource('/captain', CaptainController::class);
        Route::get('/getCaptainData', [CaptainController::class, 'getCaptainData'])->name('getCaptainData');
        Route::get('/showCaptainData/{id}', [CaptainController::class, 'showCaptainData'])->name('showCaptainData');

        // crew
        Route::resource('/crew', CrewController::class);
        Route::get('/getCrewData', [CrewController::class, 'getCrewData'])->name('getCrewData');
        // Data for a single crew member (used by owner.crew.show page)
        Route::get('/crew/{id}/data', [CrewController::class, 'showCrewData'])->name('showCrewData');

        // boats
        Route::get('/boats/{boat}/crew', [BoatController::class, 'crew'])->name('boats.crew');
        Route::get('/boats/{boat}/expenses', [BoatController::class, 'expenses'])->name('boats.expenses');

        // stock
        Route::resource('/stock', StockController::class);
        Route::get('/getStockData', [StockController::class, 'getStockData'])->name('getStockData');

        // customers
        Route::resource('/customers', CustomersController::class);
        Route::get('/getCustomerData', [CustomersController::class, 'getCustomerData'])->name('getCustomerData');
        // customer print reports
        Route::get('/reports/print/customers', [CustomersController::class, 'printCustomersReport'])->name('reports.print.customers');
        Route::get('/reports/print/customer-sales', [CustomersController::class, 'printSalesReport'])->name('reports.print.sales');

        // vendor print report
        Route::get('/reports/print/vendor/{id}', [VendorsController::class, 'printVendorReport'])->name('reports.print.vendor');

        // dalal print report
        Route::get('/reports/print/dalal/{id}', [DalalSalesController::class, 'printDalalReport'])->name('reports.print.dalal');

        // sales in customers
        Route::resource('/sales', SalesController::class);
        Route::get('/getSalesData', [SalesController::class, 'getSalesData'])->name('getSalesData');
        Route::get('/catchDetails/{id}', [SalesController::class, 'catchDetails'])->name('catchDetails');

        // reports  sales_report
        Route::get('/sales_report', [SalesReportController::class, 'index'])->name('sales-report');
        Route::get('/sales_report/print', [SalesReportController::class, 'print'])->name('sales-report.print');
        Route::get('/getSalesDataReport', [SalesReportController::class, 'getSalesData'])->name('getSalesDataReport');
        // stock_report

        Route::get('/stock_report', [StockReportController::class, 'index'])->name('stock-report');
        Route::get('/stock_report/print', [StockReportController::class, 'print'])->name('stock-report.print');
        Route::get('/getStockDataReport', [StockReportController::class, 'getStockData'])->name('getStockDataReport');
        // dalal_stock_report
        Route::get('/dalal_stock_report', [DalalStockReportController::class, 'index'])->name('dalal-stock-report');
        Route::get('/dalal_stock_report/print', [DalalStockReportController::class, 'print'])->name('dalal-stock-report.print');
        Route::get('/getDalalStockDataReport', [DalalStockReportController::class, 'getStockData'])->name('getDalalStockDataReport');

        // trip report
        Route::get('/trip_report', [TripReportController::class, 'index'])->name('trip-report');
        Route::get('/getTripDataReport', [TripReportController::class, 'getTripData'])->name('getTripDataReport');
        // trip print reports
        Route::get('/reports/print/all-trips', [TripReportController::class, 'printAllTripsReport'])->name('reports.print.all_trips');
        Route::get('/reports/print/trip/{id}', [TripReportController::class, 'printTripReport'])->name('reports.print.trip');
        // boat print reports
        Route::get('/reports/print/all-boats', [\App\Http\Controllers\Owner\Report\BoatReportController::class, 'printAllBoatsReport'])->name('reports.print.all_boats');
        Route::get('/reports/print/boat/{id}', [\App\Http\Controllers\Owner\Report\BoatReportController::class, 'printBoatReport'])->name('reports.print.boat');
        // fish_Stock_history
        Route::get('/fish_stock_history', [FishHistoryReportController::class, 'index'])->name('fish-history-report');
        Route::get('/getFishStockHistoryReport', [FishHistoryReportController::class, 'getFishHistoryData'])->name('getFishStockHistoryReport');
        Route::get('/fish_stock_history/print', [FishHistoryReportController::class, 'print'])->name('fish-history-report.print');

        // catch-records
        Route::resource('/catch', CatchController::class);
        Route::get('/getCatchData', [CatchController::class, 'getCatchData'])->name('getCatchData');
        Route::get('/printCatchReport/{id}', [CatchController::class, 'printCatchReport'])->name('printCatchReport');
        Route::get('/getFishStats', [CatchController::class, 'getFishStats'])->name('getFishStats');
        Route::get('/revenue-by-species', [CatchController::class, 'getRevenueBySpecies'])->name('getRevenueBySpecies');
        Route::get('/weight-by-species', [CatchController::class, 'getWeightBySpecies'])->name('getWeightBySpecies');
        Route::get('/monthly-performance', [CatchController::class, 'getMonthlyPerformance'])->name('getMonthlyPerformance');
        Route::get('/stats-summary', [CatchController::class, 'getStatsSummary'])->name('getStatsSummary');

        // dalal
        Route::resource('/dalal', DalalSalesController::class);
        Route::get('/getDalalData', [DalalSalesController::class, 'getDalalData'])->name('getDalalData');

        // dalal stock by boat
        Route::get('/dalal-stock-boat', [DalalStockController::class, 'showDalalBoatStock'])->name('dalal.stock.boat.show');
        Route::get('/getDalalStockBoatData', [DalalStockController::class, 'getDalalStockBoatData'])->name('getDalalStockBoatData');

        // show trips by boat_id
        Route::get('/show-dalal-stock-boat/{boat_id}', [DalalStockController::class, 'showBoat'])->name('dalal.show-boat');
        Route::get('/getShowTripBoatData/{boat_id}', [DalalStockController::class, 'getBoatTripData'])->name('getBoatTripData');

        // show dalals by trip_id
        Route::get('/show-dalal-stock-trip/{trip_id}', [DalalStockController::class, 'showTrip'])->name('dalal.show-trip');
        Route::get('/getTripDalalData/{trip_id}', [DalalStockController::class, 'getTripDalalData'])->name('getTripDalalData');

        // show transaction by dalal_id
        Route::get('/show-dalal-transaction/{dalal_id}', [DalalStockController::class, 'showDalal'])->name('dalal.show-dalal');
        Route::get('/getDalalTransactionData/{trip_id}', [DalalStockController::class, 'getDalalTransactionData'])->name('getDalalTransactionData');
        // sales_details pop model
        Route::get('/sale-details/{sale_id}', [DalalStockController::class, 'getSaleDetails'])->name('sale.details');
        Route::get('/remaining-stock/{dalal_stock_detail_id}', [DalalStockController::class, 'getRemainingStock'])->name('owner.remaining.stock');

        // dalal sales&payment
        Route::get('/getDalalPaymentData/{dalal_id}', [DalalPaymentController::class, 'getDalalPaymentData'])->name('getDalalPaymentData');
        Route::resource('/dalal-payment', DalalPaymentController::class);
        Route::get('/get-payments', [DalalPaymentController::class, 'getPayments'])->name('getPayments');

        // dalal sales  & payment  & chart
        Route::get('/top-dalals-chart', [DalalSalesController::class, 'topDalalsChart'])->name('top-dalals-chart');
        Route::get('/top-dalals-bar-chart', [DalalSalesController::class, 'topDalalsBarChart'])->name('top-dalals-bar-chart');
        Route::get('/dalal-performance-stats', [DalalSalesController::class, 'getDalalPerformanceStats'])->name('dalal-performance.stats');

        // Dalal Invoices Management
        Route::get('/dalal-invoices', [\App\Http\Controllers\Owner\DalalInvoiceController::class, 'index'])->name('dalal-invoices.index');
        Route::get('/getDalalInvoiceData', [\App\Http\Controllers\Owner\DalalInvoiceController::class, 'getInvoiceData'])->name('getDalalInvoiceData');
        Route::get('/dalal-invoices/{id}', [\App\Http\Controllers\Owner\DalalInvoiceController::class, 'show'])->name('dalal-invoices.show');
        Route::post('/dalal-invoices/{id}/accept', [\App\Http\Controllers\Owner\DalalInvoiceController::class, 'accept'])->name('dalal-invoices.accept');
        Route::post('/dalal-invoices/{id}/reject', [\App\Http\Controllers\Owner\DalalInvoiceController::class, 'reject'])->name('dalal-invoices.reject');

        Route::resource('/expenses', ExpensesController::class);

        // payroll
        Route::resource('/payrolls', PayrollController::class);
        // printable payroll report
        Route::get('/payrolls/{payroll}/print', [PayrollController::class, 'print'])->name('payrolls.print');
        Route::get('/getPayrollsData', [PayrollController::class, 'getData'])->name('getPayrollsData');
        Route::post('/payrolls/check', [PayrollController::class, 'check'])->name('payrolls.check');
        Route::post('/payrolls/percentageCheck', [PayrollController::class, 'percentageCheck'])->name('payrolls.percentageCheck');
        Route::any('/percentage', [PayrollController::class, 'getPercentage'])->name('percentage');
        Route::any('/percentageCreate', [PayrollController::class, 'percentageCreate'])->name('percentageCreate');

        Route::post('/payroll-boat-fetch', [PayrollController::class, 'fetch'])->name('payroll.fetch');
        Route::get('/payroll-boat-Periods/{boat}', [PayrollController::class, 'paidPeriods'])->name('payroll.paidPeriods');
        Route::get('/profit-loss', [ProfitLossController::class, 'index'])->name('profit.loss');
        Route::get('/profit-loss/print', [ProfitLossController::class, 'print'])->name('profit.loss.print');

        Route::get('/fish-quntity', [ReportsController::class, 'fishQuntity'])->name('fishQuntity');
        Route::get('/asset_depreciation', [ReportsController::class, 'assetDepreciation'])->name('assetDepreciation');

        Route::resource('/crew-check', CrewCheckController::class);
        Route::resource('/financial-reports', FinancialReportsController::class);
        Route::resource('/analytics', AnalyticsController::class);
        Route::resource('/documents', DocumentsController::class);
        Route::resource('/data-entry', DataEntryController::class);
        Route::resource('/daily-report', DailyReportController::class);
        Route::get('vendors/data', [VendorsController::class, 'getVendors'])->name('vendors.data');
        Route::resource('/vendors', VendorsController::class);
        Route::resource('/settings', SettingsController::class);

        Route::get('/data/maintenances', [MaintenanceController::class, 'getMaintenanceData'])->name('maintenance.data');
        Route::resource('/maintenance', MaintenanceController::class);

        Route::get('/data/inspections', [InspectionsController::class, 'getInspectionData'])->name('inspections.data');
        Route::resource('/inspections', InspectionsController::class);

        Route::get('/data/expenses', [ExpensesController::class, 'getExpenseData'])->name('expenses.data');
        Route::resource('/expenses', ExpensesController::class);

        Route::prefix('/expenses')->name('expenses.')->group(function () {
            Route::get('{expense}/print', [ExpensesController::class, 'print'])->name('print');
            Route::patch('{expense}/status', [ExpensesController::class, 'changeStatus'])->name('status');
        });

        Route::resource('/expenseables', ExpenseablesController::class);

        Route::prefix('api')->name('api.')->group(function () {
            Route::get('/boats', [ExpensesController::class, 'getBoats'])->name('boats.data');
            Route::get('/fishing-equipments', [ExpensesController::class, 'getFishingEquipments'])->name('fishing-equipments.data');
            Route::get('/available-maintenances', [ExpensesController::class, 'getAvailableMaintenances'])->name('available-maintenance.data');
        });
        Route::get('/data/fishing-equipments', [FishingEquipmentController::class, 'getData'])->name('fishing-equipments.data');
        Route::resource('/fishing-equipments', FishingEquipmentController::class);

        Route::get('boats_crew/{boat}', [BoatController::class, 'crew']);
        Route::resource('/employee', EmployeeController::class);
        Route::get('/getEmployeeData', [EmployeeController::class, 'getEmployeeData'])->name('getEmployeeData');

        // Contact submission from floating button
        Route::post('/contact', function (\Illuminate\Http\Request $request) {
            \App\Models\Contact::create([
                'name' => trim($request->first_name.' '.$request->last_name),
                'email' => $request->email,
                'phone' => $request->phone,
                'subject' => $request->subject,
                'message' => $request->message,
                'user_id' => Auth::id(),
            ]);

            return response()->json(['status' => 'success']);
        })->name('contact.store');

        // Support Ticket API Integration Routes
        Route::get('/support/priorities', [SupportTicketController::class, 'getPriorities'])->name('support.priorities');
        Route::get('/support/categories', [SupportTicketController::class, 'getCategories'])->name('support.categories');
        Route::get('/support/tickets/phone', [SupportTicketController::class, 'getUserTickets'])->name('support.tickets.user');
        Route::post('/support/ticket', [SupportTicketController::class, 'createTicket'])->name('support.ticket.create');

        Route::resource('/cities', CityController::class);
        Route::resource('/governorates', GovernorateController::class);
        Route::resource('/ports', PortController::class);
        Route::resource('/regions', RegionController::class);
    });
});
