<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('sale_details', 'dalal_stock_detail_id')) {
            return;
        }

        Schema::table('sale_details', function (Blueprint $table) {
            $table->dropForeign(['dalal_stock_detail_id']);
            $table->dropColumn('dalal_stock_detail_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // The dalal_stock_details table no longer exists; no rollback supported.
    }
};
