<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('return_details');
        Schema::dropIfExists('returns');

        if (Schema::hasColumn('month_closings', 'returns')) {
            Schema::table('month_closings', function (Blueprint $table) {
                $table->dropColumn('returns');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasColumn('month_closings', 'returns')) {
            Schema::table('month_closings', function (Blueprint $table) {
                $table->decimal('returns', 14, 2)->default(0)->after('gross_sales');
            });
        }
    }
};
