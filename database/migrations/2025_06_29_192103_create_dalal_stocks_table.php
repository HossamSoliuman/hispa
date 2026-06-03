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
        Schema::create('dalal_stocks', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('status')->default(0);
            $table->foreignId('owner_id')->constrained('users')->onDelete('cascade'); // صيّاد الرحلة
            $table->foreignId('dalal_id')->constrained('users')->onDelete('cascade'); // الدلال
            $table->foreignId('trip_id')->nullable()->constrained('trips')->onDelete('set null'); // الرحلة المرتبطة (اختياري)

            $table->decimal('total_weight', 10, 2)->default(0); // مجموع الوزن الإجمالي المرسل للدلال

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dalal_stocks');
    }
};
