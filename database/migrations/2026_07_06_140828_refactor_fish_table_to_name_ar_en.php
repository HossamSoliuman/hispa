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
            $table->string('name_ar')->nullable()->after('id');
            $table->string('name_en')->nullable()->after('name_ar');
        });

        Schema::table('fish', function (Blueprint $table) {
            $table->dropForeign(['region_id']);
            $table->dropForeign(['governorate_id']);
        });

        Schema::table('fish', function (Blueprint $table) {
            $table->dropColumn([
                'scientific_name',
                'english_name',
                'local_name_primary',
                'local_name_secondary',
                'red_sea_name',
                'arabian_gulf_name',
                'code',
                'region_id',
                'governorate_id',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fish', function (Blueprint $table) {
            $table->string('scientific_name')->nullable();
            $table->string('english_name')->nullable();
            $table->string('local_name_primary')->nullable();
            $table->string('local_name_secondary')->nullable();
            $table->string('red_sea_name')->nullable();
            $table->string('arabian_gulf_name')->nullable();
            $table->string('code')->nullable();
            $table->unsignedBigInteger('region_id')->nullable();
            $table->unsignedBigInteger('governorate_id')->nullable();

            $table->foreign('region_id')->references('id')->on('regions')->onDelete('cascade');
            $table->foreign('governorate_id')->references('id')->on('governorates')->onDelete('set null');
        });

        Schema::table('fish', function (Blueprint $table) {
            $table->dropColumn(['name_ar', 'name_en']);
        });
    }
};
