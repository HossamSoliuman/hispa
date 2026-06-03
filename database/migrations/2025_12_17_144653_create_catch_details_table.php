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
        Schema::create('catch_details', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('catch_id');

            $table->unsignedBigInteger('fish_id');
            $table->string('fish_name')->nullable();
            $table->decimal('weight', 10, 2);
            $table->decimal('price_per_kg', 10, 2)->default(0);
            $table->decimal('total_price', 10, 2)->default(0);
            $table->timestamps();

            $table->foreign('catch_id')
                ->references('id')
                ->on('catch_models')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('catch_details');
    }
};
