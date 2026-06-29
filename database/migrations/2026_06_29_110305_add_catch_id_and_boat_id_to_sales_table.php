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
        Schema::table('sales', function (Blueprint $table) {
            if (! Schema::hasColumn('sales', 'boat_id')) {
                $table->unsignedBigInteger('boat_id')->nullable()->after('trip_id');
            }

            if (! Schema::hasColumn('sales', 'catch_id')) {
                $table->unsignedBigInteger('catch_id')->nullable()->after('trip_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            if (Schema::hasColumn('sales', 'catch_id')) {
                $table->dropColumn('catch_id');
            }

            if (Schema::hasColumn('sales', 'boat_id')) {
                $table->dropColumn('boat_id');
            }
        });
    }
};
