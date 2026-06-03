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
        Schema::table('trips', function (Blueprint $table) {
            $table->string('permit_type')->nullable()->change();
            $table->dropForeign(['governorate_id']);
            $table->dropForeign(['region_id']);
            $table->dropForeign(['port_id']);
            $table->bigInteger('governorate_id')->default(0)->change();
            $table->bigInteger('region_id')->default(0)->change();
            $table->bigInteger('port_id')->default(0)->change();
        });
    }
};
