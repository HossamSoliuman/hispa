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
        Schema::create('trip_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trip_id')->constrained('trips')->onDelete('cascade');
            $table->foreignId('fish_id')->constrained('fish')->onDelete('cascade');
            $table->string('fish_name');
            $table->decimal('weight', 10, 2);
            $table->integer('quantity')->nullable();
            $table->integer('quantity_captain')->nullable();
            $table->decimal('weight_captain', 10, 2)->nullable();
            $table->integer('quantity_counter')->nullable();
            $table->decimal('weight_counter', 10, 2)->nullable();
            $table->foreignId('added_by')->constrained('users');
            $table->foreignId('corrected_by')->nullable()->constrained('users');
            $table->text('notes')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trip_details');
    }
};
