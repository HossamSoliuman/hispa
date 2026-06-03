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
        Schema::create('dalal_stock_details', function (Blueprint $table) {
            $table->id();

            $table->foreignId('dalal_stock_id')->constrained('dalal_stocks')->onDelete('cascade');
            $table->foreignId('fish_id')->constrained('fish')->onDelete('cascade'); // نوع السمك

            $table->string('fish_name')->nullable(); // اسم السمك (اختياري في حال حابب تخزنه مباشرة)
            $table->decimal('weight', 10, 2); // الوزن لهذا الصنف
            $table->bigInteger('quantity')->default(0)->nullable(); // عدد الصناديق أو الحبات

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dalal_stock_details');
    }
};
