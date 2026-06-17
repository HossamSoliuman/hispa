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
        Schema::table('month_closing_dues', function (Blueprint $table) {
            // Frozen custom percentage applied at close time (null = share-based).
            $table->decimal('custom_share_percent', 5, 2)->nullable()->after('shares');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('month_closing_dues', function (Blueprint $table) {
            $table->dropColumn('custom_share_percent');
        });
    }
};
