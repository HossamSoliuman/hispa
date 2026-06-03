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
        Schema::table('violations', function (Blueprint $table) {
            $table->foreignId('governorate_id')
                ->nullable()
                ->constrained('governorates')
                ->cascadeOnDelete();

            $table->foreignId('city_id')
                ->nullable()
                ->constrained('cities')
                ->cascadeOnDelete();

            $table->foreignId('port_id')
                ->nullable()
                ->constrained('ports')
                ->cascadeOnDelete();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('violations', function (Blueprint $table) {
            $table->dropForeign(['governorate_id']);
            $table->dropForeign(['city_id']);
            $table->dropForeign(['port_id']);

            $table->dropColumn(['governorate_id', 'city_id', 'port_id']);
        });
    }
};
