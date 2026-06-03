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
        Schema::create('trips', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('number')->unique();
            $table->string('license_number')->unique();
            $table->tinyInteger('status')->default(1);
            $table->text('cancel_reason')->nullable(); // with captain or admin

            $table->string('permit_type');
            $table->foreignId('owner_id')->constrained('users');
            $table->foreignId('captain_id')->constrained('users');

            $table->string('boat_name');
            $table->string('boat_number');
            $table->string('boat_color')->nullable();
            $table->decimal('boat_length', 8, 2)->nullable();
            $table->decimal('boat_width', 8, 2)->nullable();

            $table->time('departure_time')->nullable();       // ساعة الإبحار المتوقعة
            $table->time('return_time')->nullable();          // ساعة العودة المتوقعة
            $table->date('start_date')->nullable();           // التاريخ المقرر للانطلاق
            $table->date('end_date')->nullable();             // التاريخ المقرر للعودة
            $table->timestamp('actual_start_datetime')->nullable(); // وقت البدء الفعلي
            $table->timestamp('actual_end_datetime')->nullable(); // وقت الانهاء الفعلي
            $table->foreignId('region_id')->constrained('regions')->onDelete('cascade');
            $table->foreignId('governorate_id')->constrained('governorates')->onDelete('cascade');
            $table->foreignId('city_id')->constrained('cities')->onDelete('cascade');
            $table->foreignId('port_id')->constrained('ports')->onDelete('cascade');

            $table->string('departure_port')->nullable();
            $table->string('return_port')->nullable();

            $table->foreignId('counter_id')->nullable()->constrained('users');
            $table->foreignId('dalal_id')->nullable()->constrained('users');
            $table->string('license_attachment')->nullable();
            $table->text('notes')->nullable();
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trips');
    }
};
