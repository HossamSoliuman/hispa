<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Captains assigned to a boat for a specific trip. Captains are picked per
     * trip (a point-in-time snapshot), so the same captain can serve on
     * different boats across trips and a boat can carry more than one captain.
     */
    public function up(): void
    {
        Schema::create('trip_boat_captains', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trip_boat_id')->constrained('trip_boats')->cascadeOnDelete();
            $table->foreignId('captain_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['trip_boat_id', 'captain_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trip_boat_captains');
    }
};
