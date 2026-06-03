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
        Schema::create('fish_stock_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('fish_stock_id')->nullable();
            $table->unsignedBigInteger('fish_id');
            $table->string('operation_type'); // add, update, delete, sale, sale_update, sale_delete, transfer
            $table->integer('changed_weight')->nullable();
            $table->integer('before_weight')->nullable();
            $table->integer('after_weight')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('done_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fish_stock_histories');
    }
};
