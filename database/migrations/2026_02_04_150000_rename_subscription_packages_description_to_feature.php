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
        Schema::table('subscription_packages', function (Blueprint $table) {
            $table->text('feature_ar')->nullable()->after('sort_order');
            $table->text('feature_en')->nullable()->after('feature_ar');
        });

        $packages = \DB::table('subscription_packages')->get();
        foreach ($packages as $row) {
            \DB::table('subscription_packages')
                ->where('id', $row->id)
                ->update([
                    'feature_ar' => $row->description_ar ?? null,
                    'feature_en' => $row->description_en ?? null,
                ]);
        }

        Schema::table('subscription_packages', function (Blueprint $table) {
            $table->dropColumn(['description_ar', 'description_en']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscription_packages', function (Blueprint $table) {
            $table->text('description_ar')->nullable()->after('sort_order');
            $table->text('description_en')->nullable()->after('description_ar');
        });

        $packages = \DB::table('subscription_packages')->get();
        foreach ($packages as $row) {
            \DB::table('subscription_packages')
                ->where('id', $row->id)
                ->update([
                    'description_ar' => $row->feature_ar ?? null,
                    'description_en' => $row->feature_en ?? null,
                ]);
        }

        Schema::table('subscription_packages', function (Blueprint $table) {
            $table->dropColumn(['feature_ar', 'feature_en']);
        });
    }
};
