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
        Schema::create('custom_feature_accesses', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('feature', 64);
            $table->string('status', 20)->default('active');
            $table->timestamp('paused_at')->nullable();
            $table->foreignId('granted_by_admin_id')->nullable()->constrained('admins')->nullOnDelete();
            $table->timestamps();

            $table->unique(['user_id', 'feature']);
            $table->index(['feature', 'status']);
        });

        $legacyEmails = config('features.business_startup.emails', []);

        if ($legacyEmails !== []) {
            $timestamp = now();
            $accesses = DB::table('users')
                ->where('role', 'owner')
                ->whereIn('email', $legacyEmails)
                ->pluck('id')
                ->map(fn (int $userId): array => [
                    'user_id' => $userId,
                    'feature' => 'business_startup',
                    'status' => 'active',
                    'paused_at' => null,
                    'granted_by_admin_id' => null,
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                ])
                ->all();

            DB::table('custom_feature_accesses')->insertOrIgnore($accesses);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('custom_feature_accesses');
    }
};
