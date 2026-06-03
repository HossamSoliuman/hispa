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
        Schema::create('ifesh_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('auction_id')->constrained('ifesh_auctions')->onDelete('cascade');
            $table->foreignId('fish_stock_id')->nullable()->constrained('fish_stocks')->onDelete('set null');
            $table->foreignId('fish_id')->constrained('fish')->onDelete('cascade');
            $table->foreignId('trip_id')->nullable()->constrained('trips')->onDelete('set null');
            $table->foreignId('owner_id')->constrained('users')->onDelete('cascade');
            $table->decimal('quantity', 10, 2); // in kg
            $table->decimal('starting_price', 10, 2);
            $table->decimal('current_bid', 10, 2)->nullable();
            $table->foreignId('winner_dalal_id')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('status', ['available', 'bidding', 'sold', 'withdrawn'])->default('available');
            $table->timestamps();
            $table->softDeletes();

            $table->index('auction_id');
            $table->index('status');
            $table->index('fish_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ifesh_items');
    }
};
