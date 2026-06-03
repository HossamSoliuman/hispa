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
        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();
            // علاقات
            $table->foreignId('owner_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('boat_id')->nullable()->constrained('boats')->nullOnDelete();

            // الفترة
            $table->date('period_from');
            $table->date('period_to');

            // الأرقام المالية
            $table->decimal('total_revenues', 14, 2)->default(0);   // إجمالي الإيرادات
            $table->decimal('total_expenses', 14, 2)->default(0);   // إجمالي المصاريف
            $table->decimal('owner_percentage', 8, 2)->default(0);     // نصيب الصيّاد
            $table->decimal('owner_profit', 14, 2)->default(0);     // نصيب الصيّاد
            $table->decimal('crew_total', 14, 2)->default(0);       // ما تم توزيعه فعلياً على الطاقم
            $table->decimal('carry_over', 14, 2)->default(0);       // المبلغ المرحل (فائض أو عجز)

            $table->decimal('surplus', 14, 2)->default(0);          // إذا في فائض بعد التوزيع
            $table->decimal('deficit', 14, 2)->default(0);          // إذا في عجز بعد التوزيع
            $table->text('notes')->nullable();                      // ملاحظات إضافية (للتوضيح الإداري)

            $table->enum('status', ['open', 'closed'])->default('open');

            $table->softDeletes();
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payrolls');
    }
};
