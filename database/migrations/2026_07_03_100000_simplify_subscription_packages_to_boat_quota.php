<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * A plan is now just a boat quota + a price. The free-text feature bullet
     * lists are dropped, and the offer `price` is made nullable (the app stores
     * null when a package has no discount).
     */
    public function up(): void
    {
        Schema::table('subscription_packages', function (Blueprint $table) {
            foreach (['features', 'feature_ar', 'feature_en'] as $column) {
                if (Schema::hasColumn('subscription_packages', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        Schema::table('subscription_packages', function (Blueprint $table) {
            $table->decimal('price', 14, 2)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('subscription_packages', function (Blueprint $table) {
            $table->decimal('price', 14, 2)->default(0)->change();
            $table->json('features')->nullable();
            $table->text('feature_ar')->nullable();
            $table->text('feature_en')->nullable();
        });
    }
};
