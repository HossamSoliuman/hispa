<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crew_advances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('owner_id');
            $table->decimal('amount', 12, 2)->default(0);
            $table->date('date');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['owner_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crew_advances');
    }
};
