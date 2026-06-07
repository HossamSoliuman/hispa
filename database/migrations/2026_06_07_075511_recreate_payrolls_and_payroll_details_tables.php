<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Recreates the `payrolls` and `payroll_details` tables that were dropped by
     * the 2026_01_24_105605_drop_deprecated_tables migration. These tables are
     * still used by the per-boat payroll flow (BoatRepository, ReportsController,
     * PayrollService carry-over, PayrollRepository) and are distinct from the
     * monthly `payroll_models` / `payroll_details_models` tables.
     */
    public function up(): void
    {
        if (! Schema::hasTable('payrolls')) {
            Schema::create('payrolls', function (Blueprint $table) {
                $table->id();
                $table->foreignId('owner_id')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('boat_id')->nullable()->constrained('boats')->nullOnDelete();

                $table->date('period_from');
                $table->date('period_to');

                $table->decimal('total_revenues', 14, 2)->default(0);
                $table->decimal('total_expenses', 14, 2)->default(0);
                $table->decimal('owner_percentage', 8, 2)->default(0);
                $table->decimal('owner_profit', 14, 2)->default(0);
                $table->decimal('crew_total', 14, 2)->default(0);
                $table->decimal('carry_over', 14, 2)->default(0);

                $table->decimal('surplus', 14, 2)->default(0);
                $table->decimal('deficit', 14, 2)->default(0);
                $table->text('notes')->nullable();

                $table->enum('status', ['open', 'closed'])->default('open');

                $table->softDeletes();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('payroll_details')) {
            Schema::create('payroll_details', function (Blueprint $table) {
                $table->id();
                $table->foreignId('payroll_id')->constrained()->cascadeOnDelete();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();

                $table->enum('salary_type', ['salary', 'percentage']);
                $table->decimal('fixed_amount', 14, 2)->nullable();
                $table->decimal('percentage', 5, 2)->nullable();
                $table->decimal('calculated_salary', 14, 2)->default(0);

                $table->boolean('is_captain')->default(false);
                $table->boolean('is_crew')->default(false);

                $table->text('notes')->nullable();
                $table->softDeletes();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payroll_details');
        Schema::dropIfExists('payrolls');
    }
};
