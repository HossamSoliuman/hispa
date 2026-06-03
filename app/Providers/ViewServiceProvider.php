<?php

namespace App\Providers;

use App\Models\Sale;
use App\Models\Trip;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        View::composer('admin.partial.sidebar', function ($view) {
            $tripStatuses = __('admin.trip_statuses');

            // استخدام الكاش لمدة 60 ثانية
            $tripCounts = Cache::remember('sidebar_trip_counts', 60, function () {
                return Trip::selectRaw('status, COUNT(*) as total')
                    ->groupBy('status')
                    ->pluck('total', 'status')
                    ->toArray();
            });
            $saleCounts = Cache::remember('sidebar_sales_counts', 60, function () {
                return [
                    'all' => Sale::count(),
                    'owner' => Sale::where('seller_type', 'owner')->count(),
                    'dalal' => Sale::where('seller_type', 'dalal')->count(),
                ];
            });

            $view->with(compact('tripStatuses', 'tripCounts', 'saleCounts'));
        });

    }
}
