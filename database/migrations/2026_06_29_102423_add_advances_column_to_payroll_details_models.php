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
            $table->decimal('advances', 14, 2)->default(0)->after('deduction');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payroll_details_models', function (Blueprint $table) {
            $table->dropColumn('advances');
        });
    }
};
