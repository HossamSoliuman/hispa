<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * إضافة عمود is_featured إن لم يكن موجوداً (للتزامن مع التطبيق).
     */
    public function up(): void
    {
        if (Schema::hasColumn('subscription_packages', 'is_featured')) {
            return;
        }

        Schema::table('subscription_packages', function (Blueprint $table) {
            $table->boolean('is_featured')->default(false)->after('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasColumn('subscription_packages', 'is_featured')) {
            return;
        }

        Schema::table('subscription_packages', function (Blueprint $table) {
            $table->dropColumn('is_featured');
        });
    }
};
