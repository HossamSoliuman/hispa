<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Promote every existing single-boat trip into the new multi-boat model:
     * one trip_boats row (with the trip's boat snapshot + crew_count) and one
     * trip_boat_captains row (the trip's captain). Existing catches and sales
     * are tagged with that boat so per-boat financials reconcile with history.
     */
    public function up(): void
    {
        $now = now();

        DB::table('trips')->whereNotNull('boat_id')->orderBy('id')->each(function ($trip) use ($now) {
            $tripBoatId = DB::table('trip_boats')
                ->where('trip_id', $trip->id)
                ->where('boat_id', $trip->boat_id)
                ->value('id');

            if (! $tripBoatId) {
                $tripBoatId = DB::table('trip_boats')->insertGetId([
                    'trip_id' => $trip->id,
                    'boat_id' => $trip->boat_id,
                    'boat_name' => $trip->boat_name,
                    'boat_number' => $trip->boat_number,
                    'boat_color' => $trip->boat_color,
                    'boat_length' => $trip->boat_length,
                    'boat_width' => $trip->boat_width,
                    'crew_count' => $trip->crew_count ?? 0,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            if ($trip->captain_id) {
                $hasCaptain = DB::table('trip_boat_captains')
                    ->where('trip_boat_id', $tripBoatId)
                    ->where('captain_id', $trip->captain_id)
                    ->exists();

                if (! $hasCaptain) {
                    DB::table('trip_boat_captains')->insert([
                        'trip_boat_id' => $tripBoatId,
                        'captain_id' => $trip->captain_id,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }
            }

            DB::table('catch_models')
                ->where('trip_id', $trip->id)
                ->whereNull('boat_id')
                ->update(['boat_id' => $trip->boat_id]);

            $catchId = DB::table('catch_models')
                ->where('trip_id', $trip->id)
                ->orderBy('id')
                ->value('id');

            DB::table('sales')
                ->where('trip_id', $trip->id)
                ->whereNull('boat_id')
                ->update(['boat_id' => $trip->boat_id]);

            if ($catchId) {
                DB::table('sales')
                    ->where('trip_id', $trip->id)
                    ->whereNull('catch_id')
                    ->update(['catch_id' => $catchId]);
            }
        });
    }

    public function down(): void
    {
        DB::table('trip_boat_captains')->delete();
        DB::table('trip_boats')->delete();
        DB::table('catch_models')->update(['boat_id' => null]);
        DB::table('sales')->update(['boat_id' => null, 'catch_id' => null]);
    }
};
