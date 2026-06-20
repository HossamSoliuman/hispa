<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Each boat participating in a trip. A trip may span several boats; this row
     * snapshots the boat's identifying data (mirroring how the trip itself
     * snapshots its primary boat) so historical manifests stay stable.
     */
    public function up(): void
    {
        Schema::create('trip_boats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trip_id')->constrained('trips')->cascadeOnDelete();
            $table->foreignId('boat_id')->constrained('boats')->cascadeOnDelete();

            $table->string('boat_name')->nullable();
            $table->string('boat_number')->nullable();
            $table->string('boat_color')->nullable();
            $table->decimal('boat_length', 8, 2)->nullable();
            $table->decimal('boat_width', 8, 2)->nullable();
            $table->unsignedInteger('crew_count')->default(0);

            $table->timestamps();

            $table->unique(['trip_id', 'boat_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trip_boats');
    }
};
