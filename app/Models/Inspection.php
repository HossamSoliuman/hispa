<?php

namespace App\Models;

use App\Enums\InspectionStatus;
use Illuminate\Database\Eloquent\Model;

class Inspection extends Model
{
    protected $guarded = [];

    protected $casts = [
        'status' => InspectionStatus::class,
    ];

    public function boat()
    {
        return $this->belongsTo(Boat::class);
    }
}
