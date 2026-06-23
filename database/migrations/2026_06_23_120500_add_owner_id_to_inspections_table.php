<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('inspections', 'owner_id')) {
            Schema::table('inspections', function (Blueprint $table) {
                $table->unsignedBigInteger('owner_id')->nullable()->after('id')->index();
            });
        }

        // Backfill each inspection's owner from its boat.
        $boatOwners = DB::table('boats')->pluck('owner_id', 'id');

        DB::table('inspections')
            ->whereNull('owner_id')
            ->whereNotNull('boat_id')
            ->orderBy('id')
            ->each(function ($inspection) use ($boatOwners) {
                $ownerId = $boatOwners[$inspection->boat_id] ?? null;
                if ($ownerId) {
                    DB::table('inspections')->where('id', $inspection->id)->update(['owner_id' => $ownerId]);
                }
            });
    }

    public function down(): void
    {
        if (Schema::hasColumn('inspections', 'owner_id')) {
            Schema::table('inspections', function (Blueprint $table) {
                $table->dropColumn('owner_id');
            });
        }
    }
};
