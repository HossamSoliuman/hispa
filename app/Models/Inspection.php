<?php

namespace App\Models;

use App\Enums\InspectionStatus;
use App\Traits\BelongsToOwner;
use Illuminate\Database\Eloquent\Model;

class Inspection extends Model
{
    use BelongsToOwner;

    protected $guarded = [];

    protected $casts = [
        'status' => InspectionStatus::class,
    ];

    public function boat()
    {
        return $this->belongsTo(Boat::class);
    }
}
