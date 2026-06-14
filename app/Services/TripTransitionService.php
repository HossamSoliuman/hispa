<?php

namespace App\Services;

use App\Enums\TripStatus;
use App\Models\Trip;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class TripTransitionService
{
    public function transition(Trip $trip, TripStatus $to, ?string $cancelReason = null, ?string $actualEndDate = null): void
    {
        DB::transaction(function () use ($trip, $to, $cancelReason, $actualEndDate) {
            $trip = Trip::where('id', $trip->id)->lockForUpdate()->first();

            if ($trip->status->isTerminal()) {
                throw new \DomainException(__('trips.errors.trip_terminal'));
            }

            if (! $trip->status->canTransitionTo($to)) {
                throw new \DomainException(__('trips.errors.invalid_transition'));
            }

            if ($to === TripStatus::Cancelled) {
                if (blank($cancelReason)) {
                    throw new \DomainException(__('trips.errors.cancel_reason_required'));
                }
                $trip->cancel_reason = $cancelReason;
            }

            if ($to === TripStatus::InProgress && is_null($trip->actual_start_datetime)) {
                $trip->actual_start_datetime = now();
            }

            if ($to === TripStatus::Finished) {
                $trip->actual_end_datetime = $actualEndDate ? Carbon::parse($actualEndDate) : now();
            }

            if ($to === TripStatus::ReadyToSell && ! $trip->catches()->exists()) {
                throw new \DomainException(__('trips.errors.catch_required'));
            }

            $trip->status = $to;
            $trip->save();
        });
    }
}
