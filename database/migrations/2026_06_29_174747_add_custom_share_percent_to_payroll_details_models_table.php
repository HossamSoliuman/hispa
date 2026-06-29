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
        Schema::table('payroll_details_models', function (Blueprint $table) {
            // Snapshot of the member's نسبة خاصة at payroll time (null = equal per-head share).
            $table->decimal('custom_share_percent', 5, 2)->nullable()->after('percentage');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payroll_details_models', function (Blueprint $table) {
            $table->dropColumn('custom_share_percent');
        });
    }
};
