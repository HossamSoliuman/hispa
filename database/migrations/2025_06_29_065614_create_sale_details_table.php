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
        Schema::create('sale_details', function (Blueprint $table) {

            $table->id();
            $table->foreignId('sale_id')->constrained('sales')->onDelete('cascade');

            $table->foreignId('fish_id')->constrained('fish')->onDelete('cascade');
            $table->string('fish_name');

            $table->unsignedInteger('quantity')->nullable();
            $table->decimal('weight', 10, 2);
            $table->decimal('price_per_kilo', 10, 2);
            $table->decimal('total_price', 12, 2);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale_details');
    }
};
