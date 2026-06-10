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
        if (! Schema::hasColumn('sales', 'commission_setting_id')) {
            return;
        }

        Schema::table('sales', function (Blueprint $table) {
            $table->dropForeign(['commission_setting_id']);
            $table->dropColumn('commission_setting_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // The commission_settings table no longer exists; no rollback supported.
    }
};
