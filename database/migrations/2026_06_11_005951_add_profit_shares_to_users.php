<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Profit shares (أسهم) used to split the crew pool at month-close.
            // Default 1.00 means equal split (backward compatible).
            $table->decimal('profit_shares', 5, 2)->default(1.00)->after('salary_amount');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('profit_shares');
        });
    }
};
