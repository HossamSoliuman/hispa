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
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->enum('asset_type', ['boat', 'fishing_equipment', 'other'])->default('other');
            $table->bigInteger('boat_id')->nullable();
            $table->string('name', 199)->nullable();
            $table->text('description')->nullable();
            $table->timestamp('purchase_date')->nullable();
            $table->decimal('purchase_cost')->default(0)->nullable();
            $table->decimal('salvage_value')->default(0)->nullable();
            $table->enum('depreciation_method', ['straight_line', 'percentage'])->default('percentage');
            $table->integer('useful_life_years')->default(0)->nullable();
            $table->decimal('depreciation_rate')->default(0)->nullable();
            $table->enum('status', ['active', 'sold', 'damaged'])->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
