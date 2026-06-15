<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('month_closings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('owner_id');
            $table->unsignedSmallInteger('year');
            $table->unsignedTinyInteger('month');
            $table->string('status')->default('closed'); // closed | reopened

            // Frozen snapshot of the canonical monthly waterfall (Part 2).
            $table->decimal('gross_sales', 14, 2)->default(0);
            $table->decimal('net_sales', 14, 2)->default(0);
            $table->decimal('net_owner_revenue', 14, 2)->default(0);
            $table->decimal('trip_expenses', 14, 2)->default(0);
            $table->decimal('general_expenses', 14, 2)->default(0);
            $table->decimal('fixed_salaries', 14, 2)->default(0);
            $table->decimal('depreciation', 14, 2)->default(0);
            $table->decimal('total_expenses', 14, 2)->default(0);
            $table->decimal('net_profit', 14, 2)->default(0);
            $table->decimal('owner_percent', 5, 2)->default(50);
            $table->decimal('owner_share', 14, 2)->default(0);
            $table->decimal('crew_share', 14, 2)->default(0);
            $table->decimal('share_value', 14, 2)->default(0);
            $table->decimal('total_shares', 8, 2)->default(0);

            $table->unsignedBigInteger('closed_by')->nullable();
            $table->dateTime('closed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['owner_id', 'year', 'month']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('month_closings');
    }
};
