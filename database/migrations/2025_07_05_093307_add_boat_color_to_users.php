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
            $table->string('boat_color')->nullable()->after('boat_number');
            $table->string('boat_length')->nullable()->after('boat_color');
            $table->string('boat_width')->nullable()->after('boat_length');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('boat_color');
            $table->dropColumn('boat_length');
            $table->dropColumn('boat_width');
        });
    }
};
