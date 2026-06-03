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
        Schema::table('contacts', function (Blueprint $table) {
            $table->tinyInteger('status')->default(1)->after('user_id');
            $table->text('response')->nullable()->after('status');
            $table->string('response_by')->nullable()->after('response');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->dropColumn('response');
            $table->dropColumn('response_by');
        });
    }
};
