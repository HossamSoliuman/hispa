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
        Schema::table('assets', function (Blueprint $table) {
            $table->unsignedBigInteger('owner_id')->nullable()->after('id');
        });

        // Backfill owner_id from the asset's boat (the only owner linkage that exists).
        $boatOwners = DB::table('boats')->pluck('owner_id', 'id');

        DB::table('assets')
            ->whereNull('owner_id')
            ->whereNotNull('boat_id')
            ->orderBy('id')
            ->each(function ($asset) use ($boatOwners) {
                $ownerId = $boatOwners[$asset->boat_id] ?? null;
                if ($ownerId) {
                    DB::table('assets')->where('id', $asset->id)->update(['owner_id' => $ownerId]);
                }
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->dropColumn('owner_id');
        });
    }
};
