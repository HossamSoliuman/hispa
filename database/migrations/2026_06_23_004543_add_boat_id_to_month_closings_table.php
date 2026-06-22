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
        Schema::table('month_closings', function (Blueprint $table) {
            $table->unsignedBigInteger('boat_id')->nullable()->after('month');

            $table->dropUnique(['owner_id', 'year', 'month']);
            $table->unique(['owner_id', 'year', 'month', 'boat_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('month_closings', function (Blueprint $table) {
            $table->dropUnique(['owner_id', 'year', 'month', 'boat_id']);
            $table->unique(['owner_id', 'year', 'month']);
            $table->dropColumn('boat_id');
        });
    }
};
