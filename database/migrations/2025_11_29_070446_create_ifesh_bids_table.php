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
        Schema::create('ifesh_bids', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained('ifesh_items')->onDelete('cascade');
            $table->foreignId('dalal_id')->constrained('users')->onDelete('cascade');
            $table->decimal('bid_amount', 10, 2);
            $table->dateTime('bid_time');
            $table->enum('status', ['active', 'outbid', 'won', 'lost'])->default('active');
            $table->timestamps();

            $table->index('item_id');
            $table->index('dalal_id');
            $table->index('status');
            $table->index('bid_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ifesh_bids');
    }
};
