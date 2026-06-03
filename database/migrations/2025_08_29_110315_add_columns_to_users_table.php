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
            $table->boolean('is_vat_applicable')->default(false);
            // رخصة صيد
            $table->string('fishing_license_number')->nullable();
            $table->date('fishing_license_expiry')->nullable();

            // رخصة قيادة (خاصة بالكابتن فقط)
            $table->string('driving_license_number')->nullable();
            $table->date('driving_license_expiry')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'is_vat_applicable',
                'fishing_license_number',
                'fishing_license_expiry',
                'driving_license_number',
                'driving_license_expiry',
            ]);
        });
    }
};
