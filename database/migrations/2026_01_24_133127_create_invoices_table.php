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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('subscription_id');
            $table->unsignedBigInteger('user_id');
            $table->string('invoice_number')->unique();
            $table->decimal('amount', 14, 2)->default(0);
            $table->decimal('vat_rate', 5, 2)->default(0);
            $table->decimal('vat_amount', 14, 2)->default(0);
            $table->decimal('total_amount', 14, 2)->default(0);
            $table->string('payment_method', 50)->nullable(); // mada, visa, bank_transfer, cash
            $table->string('payment_status', 20)->default('pending'); // pending, paid, cancelled
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('payment_confirmed_at')->nullable();
            $table->unsignedBigInteger('payment_confirmed_by')->nullable();
            $table->text('payment_notes')->nullable();
            $table->text('bank_transfer_receipt')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('invoice_number');
            $table->index('payment_status');
            $table->index('user_id');
            $table->index('subscription_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
