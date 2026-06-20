<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Sales must be attributable to a boat within the trip so each boat's
     * revenue (and therefore its crew payout) can be computed independently.
     * catch_id ties the sale to the boat's catch for stock bookkeeping. Plain
     * nullable columns — SQLite cannot add a foreign key via ALTER.
     */
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->unsignedBigInteger('boat_id')->nullable()->after('trip_id');
            $table->unsignedBigInteger('catch_id')->nullable()->after('boat_id');
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn(['boat_id', 'catch_id']);
        });
    }
};
