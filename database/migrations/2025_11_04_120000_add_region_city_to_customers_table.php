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
        Schema::table('customers', function (Blueprint $table) {
            $table->foreignId('region_id')->nullable()->after('email')->constrained('regions')->nullOnDelete();
            $table->foreignId('city_id')->nullable()->after('region_id')->constrained('cities')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropForeign(['region_id']);
            $table->dropForeign(['city_id']);
            $table->dropColumn(['region_id', 'city_id']);
        });
    }
};
