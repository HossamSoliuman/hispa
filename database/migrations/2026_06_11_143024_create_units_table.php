<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->string('name_ar');
            $table->string('name_en')->nullable();
            $table->boolean('is_default')->default(false);
            $table->boolean('status')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        $now = now();
        DB::table('units')->insert([
            ['name_ar' => 'كجم', 'name_en' => 'Kg', 'is_default' => true, 'status' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name_ar' => 'شكه', 'name_en' => 'Shaka', 'is_default' => false, 'status' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name_ar' => 'بوكس', 'name_en' => 'Box', 'is_default' => false, 'status' => true, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};
