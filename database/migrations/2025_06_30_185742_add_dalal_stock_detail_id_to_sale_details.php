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
        Schema::table('sale_details', function (Blueprint $table) {
            $table->foreignId('dalal_stock_detail_id')
                ->nullable()
                ->after('sale_id')
                ->constrained('dalal_stock_details')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sale_details', function (Blueprint $table) {
            $table->dropForeign(['dalal_stock_detail_id']);
            $table->dropColumn('dalal_stock_detail_id');
        });
    }
};
