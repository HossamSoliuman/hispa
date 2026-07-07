<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Public sign-ups create a subscription that waits for admin payment
     * confirmation, so a `pending` status is needed. The old enum/check
     * constraint is relaxed to a plain string for portability and so new
     * statuses can be added without another migration.
     */
    public function up(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->string('status')->default('trial')->change();
        });
    }

    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->enum('status', ['active', 'expired', 'trial', 'suspended'])
                ->default('trial')
                ->change();
        });
    }
};
