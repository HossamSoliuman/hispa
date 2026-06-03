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
        Schema::create('violations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('captain_id');
            //            $table->string('violation_type');
            $table->tinyInteger('status')->default(1);
            $table->string('violation_date')->nullable();
            $table->string('violation_time')->nullable();
            $table->text('description')->nullable();
            $table->foreignId('region_id')->constrained()->onDelete('cascade');
            $table->string('location')->nullable();
            $table->string('reported_by')->nullable();
            $table->decimal('fine_amount', 10, 2)->nullable(); // قيمة الغرامة
            $table->unsignedBigInteger('trip_id')->nullable(); // إذا مرتبطة بجولة بحرية

            $table->foreign('captain_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('trip_id')->references('id')->on('trips')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('violations');
    }
};
