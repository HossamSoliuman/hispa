<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('startup_projects', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete();
            $table->string('name');
            $table->string('type');
            $table->date('start_date');
            $table->string('status');
            $table->text('description')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['owner_id', 'status']);
        });
        Schema::create('startup_partners', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('project_id')->constrained('startup_projects')->cascadeOnDelete();
            $table->string('name');
            $table->decimal('share_percent', 5, 2);
            $table->string('phone')->nullable();
            $table->string('partner_type');
            $table->boolean('has_salary')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
        Schema::create('startup_expense_categories', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete();
            $table->string('name_ar');
            $table->string('name_en')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
        Schema::create('startup_loans', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('project_id')->constrained('startup_projects')->cascadeOnDelete();
            $table->string('name');
            $table->string('lender');
            $table->decimal('amount', 12, 2);
            $table->date('date');
            $table->unsignedInteger('installments_count')->nullable();
            $table->decimal('installment_amount', 12, 2)->nullable();
            $table->date('first_installment_date')->nullable();
            $table->string('borne_by');
            $table->foreignId('partner_id')->nullable()->constrained('startup_partners')->restrictOnDelete();
            $table->string('status')->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
        Schema::create('startup_expenses', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('project_id')->constrained('startup_projects')->cascadeOnDelete();
            $table->foreignId('category_id')->constrained('startup_expense_categories')->restrictOnDelete();
            $table->date('date');
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('amount', 12, 2);
            $table->string('payer_type');
            $table->foreignId('partner_id')->nullable()->constrained('startup_partners')->restrictOnDelete();
            $table->foreignId('loan_id')->nullable()->constrained('startup_loans')->restrictOnDelete();
            $table->string('payment_method');
            $table->boolean('is_shared')->default(true);
            $table->string('attachment')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
        Schema::create('startup_contributions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('project_id')->constrained('startup_projects')->cascadeOnDelete();
            $table->foreignId('partner_id')->constrained('startup_partners')->restrictOnDelete();
            $table->date('date');
            $table->decimal('amount', 12, 2);
            $table->string('payment_method');
            $table->string('type');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
        Schema::create('startup_loan_payments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('loan_id')->constrained('startup_loans')->cascadeOnDelete();
            $table->date('date');
            $table->decimal('amount', 12, 2);
            $table->string('payer_type');
            $table->foreignId('partner_id')->nullable()->constrained('startup_partners')->restrictOnDelete();
            $table->string('payment_method');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        foreach (['startup_loan_payments', 'startup_contributions', 'startup_expenses', 'startup_loans', 'startup_expense_categories', 'startup_partners', 'startup_projects'] as $table) {
            Schema::dropIfExists($table);
        }
    }
};
