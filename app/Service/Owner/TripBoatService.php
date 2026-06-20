<?php

namespace App\Service\Owner;

use App\Models\Boat;
use App\Models\Trip;
use App\Models\TripBoat;
use App\Models\User;
use Illuminate\Http\Request;

class TripBoatService
{
    /**
     * Normalize raw request input (boats[][boat_id], boats[][captain_ids][])
     * into a clean boats structure, dropping blank rows and ids.
     *
     * @return array<int, array{boat_id: int, captain_ids: array<int, int>}>
     */
    public function fromRequest(Request $request): array
    {
        return collect($request->input('boats', []))
            ->map(fn ($row): array => [
                'boat_id' => (int) ($row['boat_id'] ?? 0),
                'captain_ids' => array_values(array_unique(array_filter(
                    array_map('intval', (array) ($row['captain_ids'] ?? []))
                ))),
            ])
            ->filter(fn (array $row): bool => $row['boat_id'] > 0)
            ->values()
            ->all();
    }

    /**
     * Replace a trip's boats and their per-trip captains with the given set,
     * then refresh the trip's denormalized primary boat/captain snapshot.
     *
     * @param  array<int, array{boat_id: int, captain_ids?: array<int, int>}>  $boats
     */
    public function sync(Trip $trip, array $boats): void
    {
        $keep = [];

        foreach ($boats as $row) {
            $boat = Boat::find((int) ($row['boat_id'] ?? 0));
            if (! $boat) {
                continue;
            }

            $tripBoat = TripBoat::updateOrCreate(
                ['trip_id' => $trip->id, 'boat_id' => $boat->id],
                [
                    'boat_name' => $boat->name_ar,
                    'boat_number' => $boat->number,
                    'boat_color' => $boat->color,
                    'boat_length' => $boat->length,
                    'boat_width' => $boat->width,
                    'crew_count' => User::where('boat_id', $boat->id)->where('role', 'crew')->count(),
                ]
            );

            $tripBoat->captains()->sync($row['captain_ids'] ?? []);
            $keep[] = $tripBoat->id;
        }

        $trip->tripBoats()->whereNotIn('id', $keep)->get()->each(function (TripBoat $tripBoat): void {
            $tripBoat->captains()->detach();
            $tripBoat->delete();
        });

        $this->syncPrimary($trip->refresh());
    }

    /**
     * Mirror the first attached boat/captain onto the trip's own columns so
     * legacy readers (admin views, datatables, single-boat reports) keep working.
     */
    public function syncPrimary(Trip $trip): void
    {
        $first = $trip->tripBoats()->with('captains')->orderBy('id')->first();

        if (! $first) {
            return;
        }

        $trip->update([
            'boat_id' => $first->boat_id,
            'boat_name' => $first->boat_name,
            'boat_number' => $first->boat_number,
            'boat_color' => $first->boat_color,
            'boat_length' => $first->boat_length,
            'boat_width' => $first->boat_width,
            'crew_count' => $first->crew_count,
            'captain_id' => $first->captains->first()?->id ?? $trip->captain_id,
        ]);
    }
}
