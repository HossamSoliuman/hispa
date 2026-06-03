<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CatchModel extends Model
{
    protected $guarded = [];

    protected $casts = [
        'catch_date' => 'datetime',
    ];

    public function details()
    {
        return $this->hasMany(CatchDetail::class, 'catch_id');
    }

    public function trip()
    {
        return $this->belongsTo(Trip::class, 'trip_id');
    }
}
