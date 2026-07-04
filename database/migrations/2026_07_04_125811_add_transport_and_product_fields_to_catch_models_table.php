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
        Schema::table('catch_models', function (Blueprint $table) {
            $table->string('car_type')->nullable()->after('total_amount');
            $table->string('driver_name')->nullable()->after('car_type');
            $table->string('car_plate_number')->nullable()->after('driver_name');
            $table->string('fish_source')->nullable()->after('car_plate_number');
            $table->string('temperature')->nullable()->after('fish_source');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('catch_models', function (Blueprint $table) {
            $table->dropColumn(['car_type', 'driver_name', 'car_plate_number', 'fish_source', 'temperature']);
        });
    }
};
