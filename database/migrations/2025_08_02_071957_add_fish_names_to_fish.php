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
        Schema::table('fish', function (Blueprint $table) {
            $table->string('red_sea_name')->nullable()->after('local_name_secondary');
            $table->string('arabian_gulf_name')->nullable()->after('red_sea_name');
            $table->unsignedBigInteger('region_id')->nullable()->after('arabian_gulf_name');
            $table->foreign('region_id')->references('id')->on('regions')->onDelete('cascade');

            $table->unsignedBigInteger('governorate_id')->nullable()->after('region_id');
            $table->foreign('governorate_id')->references('id')->on('governorates')->onDelete('set null');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fish', function (Blueprint $table) {
            // Drop foreign keys
            $table->dropForeign(['governorate_id']);
            $table->dropForeign(['region_id']);

            // Drop columns
            $table->dropColumn('governorate_id');
            $table->dropColumn('region_id');
            $table->dropColumn('red_sea_name');
            $table->dropColumn('arabian_gulf_name');
        });
    }
};
