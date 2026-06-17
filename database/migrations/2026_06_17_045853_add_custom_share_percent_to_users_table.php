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
        Schema::table('users', function (Blueprint $table) {
            // Optional custom cut (نسبة خاصة) of the crew pool for a single
            // member — typically a captain given an advantage over the rest.
            // Null means the member participates by profit_shares (أسهم) only.
            $table->decimal('custom_share_percent', 5, 2)->nullable()->after('profit_shares');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('custom_share_percent');
        });
    }
};
