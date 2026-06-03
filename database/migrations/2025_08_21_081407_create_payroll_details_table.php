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
        Schema::create('payroll_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payroll_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->enum('salary_type', ['salary', 'percentage']);   // نوع الراتب
            $table->decimal('fixed_amount', 14, 2)->nullable();    // قيمة الراتب الثابت
            $table->decimal('percentage', 5, 2)->nullable();       // نسبة المشاركة إن وجدت
            $table->decimal('calculated_salary', 14, 2)->default(0); // المبلغ النهائي بعد الحساب

            // ✅ إضافات مقترحة
            $table->boolean('is_captain')->default(false); // لتوضيح إذا العضو كابتن
            $table->boolean('is_crew')->default(false);   // لو الصيّاد مشارك بالتوزيع

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
        Schema::dropIfExists('payroll_details');
    }
};
