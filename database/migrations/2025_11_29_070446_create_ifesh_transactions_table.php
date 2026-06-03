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
        Schema::create('ifesh_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('auction_id')->constrained('ifesh_auctions')->onDelete('cascade');
            $table->foreignId('item_id')->constrained('ifesh_items')->onDelete('cascade');
            $table->foreignId('dalal_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('owner_id')->constrained('users')->onDelete('cascade');
            $table->decimal('final_price', 10, 2);
            $table->decimal('quantity', 10, 2);
            $table->enum('payment_status', ['pending', 'paid', 'partially_paid'])->default('pending');
            $table->foreignId('payment_method_id')->nullable()->constrained('payment_methods')->onDelete('set null');
            $table->dateTime('transaction_date');
            $table->timestamps();

            $table->index('auction_id');
            $table->index('dalal_id');
            $table->index('owner_id');
            $table->index('payment_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ifesh_transactions');
    }
};
