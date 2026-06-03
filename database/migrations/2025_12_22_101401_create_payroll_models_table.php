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
        Schema::create('payroll_models', function (Blueprint $table) {
            $table->id();
            $table->integer('year')->default(0);
            $table->integer('month')->default(0);
            $table->enum('status', ['draft', 'approved'])->default('approved');
            $table->boolean('is_paid')->default(0);
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payroll_models');
    }
};
