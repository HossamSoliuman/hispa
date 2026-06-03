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
        Schema::create('boats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->nullable()->constrained('users')->nullOnDelete();

            $table->string('name_ar');
            $table->string('name_en')->nullable();
            $table->string('number');
            $table->tinyInteger('status')->default(1);

            $table->string('length')->nullable();
            $table->string('width')->nullable();
            $table->string('color')->nullable();

            $table->string('type')->nullable();
            $table->string('license_number')->nullable();
            $table->foreignId('license_region_id')
                ->nullable()
                ->constrained('regions')
                ->nullOnDelete();

            $table->date('license_date')->nullable();
            $table->date('license_date_expire')->nullable();

            $table->string('body_number')->nullable();
            $table->string('body_type')->nullable();
            $table->string('callsign_number')->nullable();
            $table->string('serial_number')->nullable();

            $table->tinyInteger('engine_status')->default(1);
            $table->string('engine_type')->nullable();
            $table->string('engine_power')->nullable();

            $table->integer('crew_number')->default(0);
            $table->decimal('payload', 8, 2)->default(0);

            $table->foreignId('region_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('governorate_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('port_id')->nullable()->constrained()->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('boats');
    }
};
