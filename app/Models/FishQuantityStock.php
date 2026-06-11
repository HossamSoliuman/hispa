<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FishQuantityStock extends Model
{
    protected $guarded = [];

    public function fish()
    {
        return $this->belongsTo(Fish::class);
    }

    public function trip()
    {
        return $this->belongsTo(Trip::class);
    }

    public function boat()
    {
        return $this->belongsTo(Boat::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class)->withDefault();
    }
}
