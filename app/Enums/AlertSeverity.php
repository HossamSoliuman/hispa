<?php

namespace App\Enums;

enum AlertSeverity: int
{
    case Info = 1;
    case Warning = 2;
    case Critical = 3;

    /**
     * Bootstrap contextual colour used for badges, borders and tints.
     */
    public function color(): string
    {
        return match ($this) {
            self::Critical => 'danger',
            self::Warning => 'warning',
            self::Info => 'info',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Critical => 'bi-exclamation-octagon-fill',
            self::Warning => 'bi-exclamation-triangle-fill',
            self::Info => 'bi-info-circle-fill',
        };
    }

    public function label(): string
    {
        return __('owner.alerts.severity.'.$this->name);
    }
}
