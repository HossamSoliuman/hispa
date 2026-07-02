<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('month_closings', function (Blueprint $table) {
            $table->decimal('depreciation_brought_forward', 14, 2)->default(0)->after('asset_depreciation_breakdown');
            $table->decimal('depreciation_deferred', 14, 2)->default(0)->after('depreciation_brought_forward');
        });
    }

    public function down(): void
    {
        Schema::table('month_closings', function (Blueprint $table) {
            $table->dropColumn(['depreciation_brought_forward', 'depreciation_deferred']);
        });
    }
};
