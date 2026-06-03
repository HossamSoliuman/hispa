<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * السعر الأصلي إلزامي، سعر العرض (price) اختياري ويجب أن يكون أقل من الأصلي.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('subscription_packages', 'original_price')) {
            Schema::table('subscription_packages', function (Blueprint $table) {
                $table->decimal('original_price', 14, 2)->nullable()->after('price');
            });
        }

        // Backfill: where original_price is null, set it from price
        DB::table('subscription_packages')
            ->whereNull('original_price')
            ->update(['original_price' => DB::raw('price')]);

        $driver = DB::getDriverName();
        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE subscription_packages MODIFY original_price DECIMAL(14,2) NOT NULL DEFAULT 0');
            DB::statement('ALTER TABLE subscription_packages MODIFY price DECIMAL(14,2) NULL');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::getDriverName();
        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE subscription_packages MODIFY price DECIMAL(14,2) NOT NULL DEFAULT 0');
            DB::statement('ALTER TABLE subscription_packages MODIFY original_price DECIMAL(14,2) NULL');
        }
    }
};
