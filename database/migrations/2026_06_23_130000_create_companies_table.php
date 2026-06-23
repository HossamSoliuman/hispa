<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The company profile (name, registration numbers, contact details and logo)
     * used to live in the global `settings` table, so every owner shared one
     * identity. It now lives in a dedicated per-owner `companies` table.
     */
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('owner_id')->index();
            $table->string('name_ar')->nullable();
            $table->string('name_en')->nullable();
            $table->string('cr_number')->nullable();
            $table->string('record_number')->nullable();
            $table->string('vat_number')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('website')->nullable();
            $table->string('logo')->nullable();
            $table->timestamps();

            $table->unique('owner_id');
            $table->foreign('owner_id')->references('id')->on('users')->cascadeOnDelete();
        });

        $this->backfillFromGlobalSettings();
    }

    /**
     * Seed a company row for every existing owner, carrying over the previous
     * global settings values so reports keep rendering the same header.
     */
    private function backfillFromGlobalSettings(): void
    {
        $settings = DB::table('settings')->pluck('value', 'key');

        $defaults = [
            'name_ar' => $settings['site_name'] ?? null,
            'name_en' => $settings['title_en'] ?? null,
            'email' => $settings['email'] ?? null,
            'phone' => $settings['phone'] ?? null,
            'address' => $settings['address'] ?? null,
            'logo' => $settings['logo'] ?? null,
        ];

        $owners = DB::table('users')->where('role', 'owner')->get(['id', 'cr_number', 'vat_number']);

        foreach ($owners as $owner) {
            DB::table('companies')->insert($defaults + [
                'owner_id' => $owner->id,
                'cr_number' => $owner->cr_number,
                'vat_number' => $owner->vat_number,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
