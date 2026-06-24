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
        Schema::table('month_closings', function (Blueprint $table) {
            $table->json('asset_depreciation_breakdown')->nullable()->after('depreciation');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('month_closings', function (Blueprint $table) {
            $table->dropColumn('asset_depreciation_breakdown');
        });
    }
};
