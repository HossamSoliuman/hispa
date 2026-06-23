<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Master/reference tables that were global in the single-owner era and now
     * need to be scoped per owner.
     *
     * @var list<string>
     */
    private array $tables = [
        'regions',
        'governorates',
        'ports',
        'fish',
        'units',
        'payment_methods',
        'boat_types',
        'categories',
    ];

    public function up(): void
    {
        foreach ($this->tables as $table) {
            if (! Schema::hasColumn($table, 'owner_id')) {
                Schema::table($table, function (Blueprint $blueprint) {
                    $blueprint->unsignedBigInteger('owner_id')->nullable()->after('id')->index();
                });
            }
        }

        // Backfill: every existing row belonged to the original (first) owner.
        $firstOwnerId = DB::table('users')
            ->where('role', 'owner')
            ->orderBy('id')
            ->value('id');

        if ($firstOwnerId !== null) {
            foreach ($this->tables as $table) {
                DB::table($table)->whereNull('owner_id')->update(['owner_id' => $firstOwnerId]);
            }
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $table) {
            if (Schema::hasColumn($table, 'owner_id')) {
                Schema::table($table, function (Blueprint $blueprint) {
                    $blueprint->dropColumn('owner_id');
                });
            }
        }
    }
};
