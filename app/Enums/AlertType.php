<?php

namespace App\Enums;

/**
 * Catalogue of auto-derived owner warnings. The backing value doubles as the
 * translation sub-key under `owner.alerts.*`, keeping the catalogue declarative:
 * a new warning = one case here + one translation block + one service builder.
 */
enum AlertType: string
{
    case TripOverdue = 'trip_overdue';
    case CaptainFishingLicense = 'captain_fishing_license';
    case CaptainDrivingLicense = 'captain_driving_license';
    case CrewFishingLicense = 'crew_fishing_license';
    case CrewResidence = 'crew_residence';
    case BoatLicense = 'boat_license';
    case InspectionDue = 'inspection_due';
    case MaintenanceDue = 'maintenance_due';

    public function title(): string
    {
        return __("owner.alerts.{$this->value}.title");
    }

    public function icon(): string
    {
        return match ($this) {
            self::TripOverdue => 'bi-hourglass-bottom',
            self::CaptainFishingLicense, self::CrewFishingLicense => 'bi-card-checklist',
            self::CaptainDrivingLicense => 'bi-truck',
            self::CrewResidence => 'bi-person-vcard',
            self::BoatLicense => 'bi-file-earmark-text',
            self::InspectionDue => 'bi-clipboard-check',
            self::MaintenanceDue => 'bi-tools',
        };
    }
}
