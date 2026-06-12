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
            $table->boolean('is_paid')->default(false)->after('final_salary');
            $table->dateTime('paid_at')->nullable()->after('is_paid');
            $table->decimal('paid_amount', 14, 2)->nullable()->after('paid_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payroll_details_models', function (Blueprint $table) {
            $table->dropColumn(['is_paid', 'paid_at', 'paid_amount']);
        });
    }
};
