<?php

namespace App\Enums;

enum InspectionStatus: string
{
    // case EXCELLENT = 'excellent';
    // case GOOD = 'good';
    // case MID = 'mid';
    // case POOR = 'poor';
    // case INACTIVE = 'inactive';

    case CURRENT = 'current';
    case ENDED = 'ended';

    public function label(): string
    {
        return __("owner.inspection.status.$this->value");
    }
}
