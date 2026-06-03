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
        Schema::table('fish_quantity_stocks', function (Blueprint $table) {
            $table->unsignedBigInteger('trip_id')->nullable();
            $table->unsignedBigInteger('catch_id')->nullable();
            $table->decimal('price_per_kg', 10, 2)->default(0);
            $table->unsignedBigInteger('boat_id')->nullable();
        });
    }
};
