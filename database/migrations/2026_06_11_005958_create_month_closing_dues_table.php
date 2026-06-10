<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('month_closing_dues', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('month_closing_id');
            $table->unsignedBigInteger('user_id');
            $table->string('member_name')->nullable();
            $table->string('role')->nullable();
            $table->decimal('shares', 8, 2)->default(1);
            $table->decimal('share_value', 14, 2)->default(0);
            $table->decimal('due_amount', 14, 2)->default(0);
            $table->decimal('advances', 14, 2)->default(0);
            $table->decimal('paid_amount', 14, 2)->default(0);
            $table->decimal('remaining', 14, 2)->default(0);
            $table->boolean('is_paid')->default(false);
            $table->dateTime('paid_at')->nullable();
            $table->timestamps();

            $table->foreign('month_closing_id')->references('id')->on('month_closings')->cascadeOnDelete();
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('month_closing_dues');
    }
};
