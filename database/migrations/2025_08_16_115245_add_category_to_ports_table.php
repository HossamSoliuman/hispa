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
        Schema::table('ports', function (Blueprint $table) {
            $table->string('category_ar')->after('name_en');
            $table->string('category_en')->after('category_ar');
            $table->string('address')->nullable()->after('category_en');
            $table->string('lat')->nullable()->after('address');
            $table->string('long')->nullable()->after('lat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ports', function (Blueprint $table) {

            $table->dropColumn('category_ar');
            $table->dropColumn('category_en');
            $table->dropColumn('address');
            $table->dropColumn('lat');
            $table->dropColumn('long');

        });
    }
};
