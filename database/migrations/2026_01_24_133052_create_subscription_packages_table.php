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
        Schema::create('subscription_packages', function (Blueprint $table) {
            $table->id();
            $table->string('name_ar')->nullable();
            $table->string('name_en')->nullable();
            $table->integer('boats_count')->default(1); // عدد القوارب المسموح بها
            $table->decimal('price', 14, 2)->default(0); // السعر
            $table->enum('duration_type', ['monthly', 'quarterly', 'yearly'])->default('monthly'); // نوع المدة
            $table->json('features')->nullable(); // المميزات
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->text('description_ar')->nullable();
            $table->text('description_en')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_packages');
    }
};
