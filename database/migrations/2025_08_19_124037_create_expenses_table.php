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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('number')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('boat_id')->nullable()->constrained('boats')->nullOnDelete();

            $table->decimal('total_price', 12, 2); // السعر الإجمالي
            $table->enum('discount_type', ['percentage', 'fixed'])->nullable(); // نوع الخصم
            $table->decimal('discount_value', 12, 2)->default(0); // قيمة الخصم
            $table->decimal('final_price', 12, 2)->nullable(); // السعر بعد الخصم

            $table->enum('status', ['paid', 'pending'])->default('pending');
            $table->foreignId('vendor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('payment_method_id')->nullable()->constrained('payment_methods')->nullOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            // $table->nullableMorphs('expensable');
            $table->string('attachment')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
