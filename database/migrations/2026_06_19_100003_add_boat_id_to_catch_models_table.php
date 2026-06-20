<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * A catch now belongs to a specific boat within a trip (one catch per
     * trip+boat). Plain nullable column — SQLite cannot add a foreign key via
     * ALTER, matching the existing fish_quantity_stocks.boat_id convention.
     */
    public function up(): void
    {
        Schema::table('catch_models', function (Blueprint $table) {
            $table->unsignedBigInteger('boat_id')->nullable()->after('trip_id');
        });
    }

    public function down(): void
    {
        Schema::table('catch_models', function (Blueprint $table) {
            $table->dropColumn('boat_id');
        });
    }
};
