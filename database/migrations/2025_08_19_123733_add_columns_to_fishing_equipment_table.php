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
        Schema::table('fishing_equipment', function (Blueprint $table) {
            $table->string('name_en')
                ->nullable()
                ->after('name');
            $table->foreignId('owner_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->after('name_en');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fishing_equipment', function (Blueprint $table) {
            $table->dropColumn('name_en');
            $table->dropColumn('owner_id');
        });
    }
};
