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
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique();
            $table->enum('seller_type', ['owner', 'dalal']);
            $table->foreignId('seller_id')->constrained('users');
            $table->foreignId('trip_id')->nullable()->constrained('trips')->onDelete('cascade');

            $table->foreignId('customer_id')->nullable()->constrained('customers');
            $table->string('customer_name')->nullable();

            $table->foreignId('payment_method_id')->nullable()->constrained('payment_methods');
            $table->string('payment_method')->nullable();

            $table->foreignId('commission_setting_id')->nullable()->constrained('commission_settings');
            $table->decimal('commission_rate', 5, 2)->nullable();
            $table->decimal('commission_amount', 12, 2)->nullable();

            $table->decimal('labor_rate', 5, 2)->nullable();
            $table->decimal('labor_amount', 12, 2)->nullable();

            $table->decimal('total_price', 15, 2)->default(0);
            $table->decimal('net_owner_amount', 12, 2)->nullable();

            $table->timestamp('sale_datetime');
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
        Schema::dropIfExists('sales');
    }
};
