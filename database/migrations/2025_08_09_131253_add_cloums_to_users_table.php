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
        Schema::table('users', function (Blueprint $table) {

            $table->foreignId('captain_id')
                ->after('owner_id')
                ->nullable()
                ->constrained('users') // references id by default
                ->nullOnDelete();

            $table->string('job_title')->nullable()->after('nationality');
            $table->string('date_appointment')->nullable()->after('job_title'); // تاريخ التعيين
            $table->string('emergency_contact')->nullable()->after('date_appointment');
            $table->string('emergency_number')->nullable()->after('emergency_contact');

            $table->string('residence_number')->nullable()->after('emergency_number');
            $table->string('residence_start_date')->nullable()->after('residence_number');
            $table->string('residence_end_date')->nullable()->after('residence_start_date');

            $table->string('id_attachment')->nullable()->after('residence_end_date');
            $table->enum('salary_type', ['salary', 'percentage'])->nullable()->after('id_attachment');

            $table->decimal('salary_amount', 10, 2)->nullable()->after('salary_type');
            $table->string('bank_name')->nullable()->after('salary_amount');
            $table->string('account_number')->nullable()->after('bank_name');
            $table->string('IBAN')->nullable()->after('account_number');
            $table->string('passport_number')->nullable()->after('id_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('job_title');
            $table->dropColumn('date_appointment');
            $table->dropColumn('emergency_contact');
            $table->dropColumn('emergency_number');
            $table->dropColumn('residence_number');
            $table->dropColumn('residence_start_date');
            $table->dropColumn('residence_end_date');
            $table->dropColumn('id_attachment');
            $table->dropColumn('salary_type');
            $table->dropColumn('salary_amount');
            $table->dropColumn('bank_name');
            $table->dropColumn('account_number');
            $table->dropColumn('IBAN');

        });
    }
};
