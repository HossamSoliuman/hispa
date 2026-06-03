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
        Schema::create('payroll_details_models', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('payroll_id')->nullable();
            $table->bigInteger('user_id')->nullable();
            $table->decimal('base_salary')->nullable();
            $table->decimal('percentage')->nullable();
            $table->decimal('sales_amount')->nullable();
            $table->decimal('increase')->nullable();
            $table->decimal('deduction')->nullable();
            $table->decimal('final_salary')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payroll_details_models');
    }
};
