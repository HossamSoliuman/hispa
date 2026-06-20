<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Owner dashboard alert thresholds
    |--------------------------------------------------------------------------
    |
    | Day-based windows that drive the auto-derived owner warnings computed by
    | App\Service\Owner\OwnerAlertService. A date within `critical_days` (or
    | already past) is a Critical alert; within `warning_days` it is a Warning;
    | outside both windows no alert is produced.
    |
    */

    'license_expiry' => ['warning_days' => 30, 'critical_days' => 7],
    'residence_expiry' => ['warning_days' => 30, 'critical_days' => 7],
    'boat_license_expiry' => ['warning_days' => 30, 'critical_days' => 7],
    'inspection_due' => ['warning_days' => 14, 'critical_days' => 3],
    'maintenance_due' => ['warning_days' => 14, 'critical_days' => 3],

    // A trip past its expected end is a Warning, escalating to Critical once it
    // has been overdue by more than this many hours.
    'trip_overdue' => ['critical_hours' => 48],

    // The dashboard card shows at most this many alerts (highest severity first).
    'max_visible' => 6,

];
