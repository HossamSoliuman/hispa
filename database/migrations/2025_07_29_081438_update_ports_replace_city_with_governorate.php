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
        Schema::table('ports', function (Blueprint $table) {
            // Drop the foreign key constraint first
            $table->dropForeign(['city_id']);

            // Then drop the column
            $table->dropColumn('city_id');

            // Add the new foreign key
            $table->foreignId('governorate_id')
                ->nullable()
                ->constrained('governorates')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('ports', function (Blueprint $table) {
            // Drop the new foreign key
            $table->dropForeign(['governorate_id']);
            $table->dropColumn('governorate_id');

            // Re-add the old column and its constraint
            $table->foreignId('city_id')
                ->nullable()
                ->constrained('cities')
                ->cascadeOnDelete();
        });
    }
};
