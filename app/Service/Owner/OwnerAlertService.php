<?php

namespace App\Service\Owner;

use App\Enums\AlertSeverity;
use App\Enums\AlertType;
use App\Enums\InspectionStatus;
use App\Enums\TripStatus;
use App\Models\Boat;
use App\Models\Inspection;
use App\Models\Maintenance;
use App\Models\MonthClosingDue;
use App\Models\Trip;
use App\Models\User;
use App\Support\Alert;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * Computes the owner's actionable warnings from business data on the fly.
 *
 * Every query is explicitly scoped to the given owner id (it does not rely on
 * the authenticated user), so it stays correct for polling, tests and any
 * future scheduled use. Thresholds come from config/alerts.php.
 */
final class OwnerAlertService
{
    /**
     * All current alerts for an owner, highest severity first then soonest due.
     *
     * @return \Illuminate\Support\Collection<int, \App\Support\Alert>
     */
    public function for(int $ownerId): Collection
    {
        return collect()
            ->merge($this->tripOverdue($ownerId))
            ->merge($this->tripsInProgress($ownerId))
            ->merge($this->userDateExpiring($ownerId, 'captain', 'fishing_license_expiry', AlertType::CaptainFishingLicense, 'license_expiry', 'owner.captain.show'))
            ->merge($this->userDateExpiring($ownerId, 'captain', 'driving_license_expiry', AlertType::CaptainDrivingLicense, 'license_expiry', 'owner.captain.show'))
            ->merge($this->userDateExpiring($ownerId, 'crew', 'fishing_license_expiry', AlertType::CrewFishingLicense, 'license_expiry', 'owner.crew.show'))
            ->merge($this->residenceExpiring($ownerId, 'crew', AlertType::CrewResidence, 'owner.crew.show'))
            ->merge($this->residenceExpiring($ownerId, 'captain', AlertType::CaptainResidence, 'owner.captain.show'))
            ->merge($this->boatLicenseExpiring($ownerId))
            ->merge($this->inspectionsDue($ownerId))
            ->merge($this->maintenanceDue($ownerId))
            ->merge($this->unpaidDues($ownerId))
            ->sort(function (Alert $a, Alert $b): int {
                return ($b->severity->value <=> $a->severity->value)
                    ?: (($a->dueAt?->timestamp ?? PHP_INT_MAX) <=> ($b->dueAt?->timestamp ?? PHP_INT_MAX));
            })
            ->values();
    }

    /**
     * Count of alerts per severity name (Critical/Warning/Info), for the badge.
     *
     * @param  \Illuminate\Support\Collection<int, \App\Support\Alert>  $alerts
     * @return array{total: int, critical: int, warning: int, info: int}
     */
    public function summarize(Collection $alerts): array
    {
        return [
            'total' => $alerts->count(),
            'critical' => $alerts->where('severity', AlertSeverity::Critical)->count(),
            'warning' => $alerts->where('severity', AlertSeverity::Warning)->count(),
            'info' => $alerts->where('severity', AlertSeverity::Info)->count(),
        ];
    }

    /**
     * @return \Illuminate\Support\Collection<int, \App\Support\Alert>
     */
    private function tripOverdue(int $ownerId): Collection
    {
        $criticalHours = (int) config('alerts.trip_overdue.critical_hours');

        return Trip::query()
            ->where('owner_id', $ownerId)
            ->whereIn('status', [TripStatus::New->value, TripStatus::InProgress->value])
            ->whereNotNull('end_date')
            ->where('end_date', '<', now())
            ->with('boat:id,name_ar,name_en')
            ->get()
            ->map(function (Trip $trip) use ($criticalHours): Alert {
                $overdueHours = abs($trip->end_date->diffInHours(now()));

                return new Alert(
                    type: AlertType::TripOverdue,
                    severity: $overdueHours > $criticalHours ? AlertSeverity::Critical : AlertSeverity::Warning,
                    title: AlertType::TripOverdue->title(),
                    message: __('owner.alerts.trip_overdue.message', [
                        'trip' => $trip->number,
                        'boat' => $trip->boat?->name ?: $trip->boat_name,
                        'date' => $trip->end_date->translatedFormat('d M Y'),
                    ]),
                    url: route('owner.trips.show', $trip),
                    dueAt: $trip->end_date,
                );
            });
    }

    /**
     * Trips currently at sea (in progress and not yet past their expected end).
     * Purely informational — overdue trips are handled by {@see tripOverdue()}.
     *
     * @return \Illuminate\Support\Collection<int, \App\Support\Alert>
     */
    private function tripsInProgress(int $ownerId): Collection
    {
        return Trip::query()
            ->where('owner_id', $ownerId)
            ->where('status', TripStatus::InProgress->value)
            ->where(function ($query): void {
                $query->whereNull('end_date')->orWhere('end_date', '>=', now());
            })
            ->with('boat:id,name_ar,name_en')
            ->get()
            ->map(function (Trip $trip): Alert {
                return new Alert(
                    type: AlertType::TripInProgress,
                    severity: AlertSeverity::Info,
                    title: AlertType::TripInProgress->title(),
                    message: __('owner.alerts.trip_in_progress.message', [
                        'trip' => $trip->number,
                        'boat' => $trip->boat?->name ?: $trip->boat_name,
                        'date' => $trip->end_date?->translatedFormat('d M Y') ?? '—',
                    ]),
                    url: route('owner.trips.show', $trip),
                    dueAt: $trip->end_date,
                );
            });
    }

    /**
     * Reusable builder for a `date` column on owner users of a given role.
     *
     * @return \Illuminate\Support\Collection<int, \App\Support\Alert>
     */
    private function userDateExpiring(int $ownerId, string $role, string $column, AlertType $type, string $configKey, string $routeName): Collection
    {
        $warningDays = (int) config("alerts.$configKey.warning_days");

        return User::query()
            ->where('owner_id', $ownerId)
            ->where('role', $role)
            ->active()
            ->whereNotNull($column)
            ->whereDate($column, '<=', today()->addDays($warningDays)->toDateString())
            ->get()
            ->map(function (User $user) use ($column, $type, $configKey, $routeName): Alert {
                $due = Carbon::parse($user->{$column});

                return $this->dateAlert($type, $configKey, $due, [
                    'name' => $user->name,
                    'date' => $due->translatedFormat('d M Y'),
                ], route($routeName, $user->id));
            });
    }

    /**
     * Residence/iqama expiry for owner users of a given role (crew or captain).
     * `residence_end_date` is a free-text string column that may hold blanks or
     * garbage, so it is parsed defensively in PHP rather than filtered in SQL.
     *
     * @return \Illuminate\Support\Collection<int, \App\Support\Alert>
     */
    private function residenceExpiring(int $ownerId, string $role, AlertType $type, string $routeName): Collection
    {
        $boundary = today()->addDays((int) config('alerts.residence_expiry.warning_days'))->endOfDay();

        return User::query()
            ->where('owner_id', $ownerId)
            ->where('role', $role)
            ->active()
            ->whereNotNull('residence_end_date')
            ->where('residence_end_date', '!=', '')
            ->get()
            ->map(function (User $user) use ($type, $routeName): ?Alert {
                try {
                    $due = Carbon::parse($user->residence_end_date);
                } catch (\Throwable) {
                    return null;
                }

                return $this->dateAlert($type, 'residence_expiry', $due, [
                    'name' => $user->name,
                    'date' => $due->translatedFormat('d M Y'),
                ], route($routeName, $user->id));
            })
            ->filter(fn (?Alert $alert): bool => $alert !== null && $alert->dueAt->lte($boundary))
            ->values();
    }

    /**
     * @return \Illuminate\Support\Collection<int, \App\Support\Alert>
     */
    private function boatLicenseExpiring(int $ownerId): Collection
    {
        $warningDays = (int) config('alerts.boat_license_expiry.warning_days');

        return Boat::query()
            ->where('owner_id', $ownerId)
            ->active()
            ->whereNotNull('license_date_expire')
            ->whereDate('license_date_expire', '<=', today()->addDays($warningDays)->toDateString())
            ->get()
            ->map(function (Boat $boat): Alert {
                $due = Carbon::parse($boat->license_date_expire);

                return $this->dateAlert(AlertType::BoatLicense, 'boat_license_expiry', $due, [
                    'boat' => $boat->name,
                    'date' => $due->translatedFormat('d M Y'),
                ], route('owner.boats.show', $boat->id));
            });
    }

    /**
     * Boat inspections due, using the most recent `current` inspection per boat.
     *
     * @return \Illuminate\Support\Collection<int, \App\Support\Alert>
     */
    private function inspectionsDue(int $ownerId): Collection
    {
        $boundary = today()->addDays((int) config('alerts.inspection_due.warning_days'))->endOfDay();
        $boatIds = Boat::where('owner_id', $ownerId)->active()->pluck('id');

        return Inspection::query()
            ->whereIn('boat_id', $boatIds)
            ->where('status', InspectionStatus::CURRENT->value)
            ->whereNotNull('next_check')
            ->with('boat:id,name_ar,name_en')
            ->orderByDesc('check_date')
            ->orderByDesc('id')
            ->get()
            ->unique('boat_id')
            ->map(function (Inspection $inspection): Alert {
                $due = Carbon::parse($inspection->next_check);

                return $this->dateAlert(AlertType::InspectionDue, 'inspection_due', $due, [
                    'boat' => $inspection->boat?->name ?? '-',
                    'date' => $due->translatedFormat('d M Y'),
                ], $inspection->boat ? route('owner.boats.show', $inspection->boat_id) : null);
            })
            ->filter(fn (Alert $alert): bool => $alert->dueAt->lte($boundary))
            ->values();
    }

    /**
     * Maintenance due, using the most recent maintenance record per boat (mirrors
     * {@see inspectionsDue()}). Only the latest record's `next_maintenance_date`
     * can fire, so logging fresh maintenance with a later date clears the alert.
     *
     * @return \Illuminate\Support\Collection<int, \App\Support\Alert>
     */
    private function maintenanceDue(int $ownerId): Collection
    {
        $boundary = today()->addDays((int) config('alerts.maintenance_due.warning_days'))->endOfDay();

        return Maintenance::query()
            ->where('owner_id', $ownerId)
            ->with('boat:id,name_ar,name_en')
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->get()
            ->unique('boat_id')
            ->filter(fn (Maintenance $maintenance): bool => $maintenance->next_maintenance_date !== null
                && $maintenance->next_maintenance_date->lte($boundary))
            ->map(function (Maintenance $maintenance): Alert {
                $due = $maintenance->next_maintenance_date;

                return $this->dateAlert(AlertType::MaintenanceDue, 'maintenance_due', $due, [
                    'boat' => $maintenance->boat?->name ?? '-',
                    'date' => $due->translatedFormat('d M Y'),
                ], route('owner.maintenance.index'));
            })
            ->values();
    }

    /**
     * Outstanding crew/captain dues, aggregated to one alert per person across
     * all of their unpaid month closings. Severity escalates to Critical once
     * the oldest unpaid closing is older than the configured threshold.
     *
     * @return \Illuminate\Support\Collection<int, \App\Support\Alert>
     */
    private function unpaidDues(int $ownerId): Collection
    {
        $criticalBefore = today()->subDays((int) config('alerts.unpaid_dues.critical_days'))->startOfDay();

        return MonthClosingDue::query()
            ->where('is_paid', false)
            ->where('remaining', '>', 0)
            ->whereHas('monthClosing', fn ($query) => $query->where('owner_id', $ownerId))
            ->with('monthClosing:id,owner_id,closed_at')
            ->get()
            ->groupBy('user_id')
            ->map(function (Collection $dues) use ($criticalBefore): Alert {
                $first = $dues->first();
                $total = (float) $dues->sum('remaining');
                $oldestClosedAt = $dues
                    ->map(fn (MonthClosingDue $due) => $due->monthClosing?->closed_at)
                    ->filter()
                    ->sort()
                    ->first();

                return new Alert(
                    type: AlertType::UnpaidDues,
                    severity: $oldestClosedAt && $oldestClosedAt->lt($criticalBefore)
                        ? AlertSeverity::Critical
                        : AlertSeverity::Warning,
                    title: AlertType::UnpaidDues->title(),
                    message: __('owner.alerts.unpaid_dues.message', [
                        'name' => $first->member_name,
                        'amount' => number_format($total, 2),
                    ]),
                    url: $this->duesUrlForRole($first->role, (int) $first->user_id),
                    dueAt: $oldestClosedAt,
                );
            })
            ->values();
    }

    /**
     * Best link for a person's outstanding dues: their profile when the role has
     * one, otherwise the month-closing list.
     */
    private function duesUrlForRole(?string $role, int $userId): string
    {
        return match ($role) {
            'captain' => route('owner.captain.show', $userId),
            'crew' => route('owner.crew.show', $userId),
            default => route('owner.month-closing.index'),
        };
    }

    /**
     * Build a date-driven alert, deriving its severity from the threshold config.
     *
     * @param  array<string, string>  $messageParams
     */
    private function dateAlert(AlertType $type, string $configKey, Carbon $due, array $messageParams, ?string $url): Alert
    {
        return new Alert(
            type: $type,
            severity: $this->severityForDate($due, $configKey),
            title: $type->title(),
            message: __("owner.alerts.{$type->value}.message", $messageParams),
            url: $url,
            dueAt: $due,
        );
    }

    /**
     * A date already past or within `critical_days` is Critical; otherwise (it
     * is still inside `warning_days`, guaranteed by the caller) it is a Warning.
     */
    private function severityForDate(Carbon $due, string $configKey): AlertSeverity
    {
        $criticalDays = (int) config("alerts.$configKey.critical_days");

        return $due->lte(today()->addDays($criticalDays)->endOfDay())
            ? AlertSeverity::Critical
            : AlertSeverity::Warning;
    }
}
