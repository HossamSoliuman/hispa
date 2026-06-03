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
        Schema::create('trip_fish_prices', function (Blueprint $table) {
            $table->id();

            $table->foreignId('trip_id')->nullable()->constrained('trips')->onDelete('cascade');
            $table->foreignId('fish_id')->constrained('fish')->onDelete('cascade');

            $table->enum('role', ['owner', 'dalal']); // السعر مخصص للصيّاد أو الدلال
            $table->foreignId('user_id')->constrained('users'); // الصيّاد أو الدلال المعني

            $table->decimal('price_per_kilo', 10, 2); // السعر الخاص لهذي الرحلة
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trip_fish_prices');
    }
};
