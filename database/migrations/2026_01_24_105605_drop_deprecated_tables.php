<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::table('users', function (\Illuminate\Database\Schema\Blueprint $table) {
            if (Schema::hasColumn('users', 'commission_setting_id')) {
                $table->dropForeign(['commission_setting_id']);
                $table->dropColumn('commission_setting_id');
            }
            if (Schema::hasColumn('users', 'city_id')) {
                $table->dropForeign(['city_id']);
                $table->dropColumn('city_id');
            }
        });

        if (Schema::hasColumn('customers', 'city_id')) {
            Schema::table('customers', function (\Illuminate\Database\Schema\Blueprint $table) {
                $table->dropForeign(['city_id']);
                $table->dropColumn('city_id');
            });
        }

        if (Schema::hasColumn('trips', 'city_id')) {
            Schema::table('trips', function (\Illuminate\Database\Schema\Blueprint $table) {
                $table->dropForeign(['city_id']);
                $table->dropColumn('city_id');
            });
        }

        $tables = [
            'cities',
            'commission_settings',
            'dalal_stocks',
            'dalal_stock_details',
            'equipment_season',
            // 'expenseables',
            'expense_fishing_equipment',
            'fishing_equipment',
            'fishing_tools',
            'fish_stocks',
            // 'fish_stock_histories',
            'ifesh_auctions',
            'ifesh_bids',
            'ifesh_items',
            'ifesh_transactions',
            // 'jobs',
            // 'job_batches',
            'payments',
            // 'payrolls',         // still used by the per-boat payroll flow; kept
            // 'payroll_details',  // still used by the per-boat payroll flow; kept
            // 'port_boat_types',
            'seasons',
            'season_fishing_tool',
            'support_services',
            'trip_details',
            'trip_fish_prices',
            'user_requests',
            'verifications',
        ];

        foreach ($tables as $table) {
            Schema::dropIfExists($table);
        }

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No rollback supported for destructive cleanup migration
    }
};
