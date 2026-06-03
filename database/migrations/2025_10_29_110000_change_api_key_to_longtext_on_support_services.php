<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeApiKeyToLongtextOnSupportServices extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('support_services')) {
            return;
        }

        Schema::table('support_services', function (Blueprint $table) {
            $table->longText('api_key')->nullable()->change();
        });
    }

    public function down()
    {
        if (! Schema::hasTable('support_services')) {
            return;
        }

        Schema::table('support_services', function (Blueprint $table) {
            $table->string('api_key')->nullable()->change();
        });
    }
}
