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
        Schema::create('fish', function (Blueprint $table) {
            $table->id();
            $table->string('scientific_name');      // الاسم العلمي
            $table->string('english_name')->nullable();         // الاسم الإنجليزي
            $table->string('local_name_primary')->nullable();   // الاسم المحلي (رئيسي)
            $table->string('local_name_secondary')->nullable(); // اسم محلي آخر (اختياري)
            $table->tinyInteger('status')->default(1);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fish');
    }
};
