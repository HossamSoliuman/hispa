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
        Schema::create('port_boat_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('port_id')->constrained()->onDelete('cascade');
            $table->foreignId('boat_type_id')->constrained()->onDelete('cascade');
            $table->integer('max')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('port_boat_types');
    }
};
