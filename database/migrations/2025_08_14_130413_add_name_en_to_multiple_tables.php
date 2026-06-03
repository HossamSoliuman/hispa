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
        Schema::table('regions', function (Blueprint $table) {
            $table->string('name_en')->nullable()->after('name');
        });

        Schema::table('governorates', function (Blueprint $table) {
            $table->string('name_en')->nullable()->after('name');
        });

        Schema::table('ports', function (Blueprint $table) {
            $table->string('name_en')->nullable()->after('name');
        });

        Schema::table('payment_methods', function (Blueprint $table) {
            $table->string('name_en')->nullable()->after('name');
        });

        Schema::table('trips', function (Blueprint $table) {
            $table->string('name_en')->nullable()->after('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('regions', function (Blueprint $table) {
            $table->dropColumn('name_en');
        });

        Schema::table('governorates', function (Blueprint $table) {
            $table->dropColumn('name_en');
        });

        Schema::table('ports', function (Blueprint $table) {
            $table->dropColumn('name_en');
        });

        Schema::table('payment_methods', function (Blueprint $table) {
            $table->dropColumn('name_en');
        });

        Schema::table('trips', function (Blueprint $table) {
            $table->dropColumn('name_en');
        });
    }
};
