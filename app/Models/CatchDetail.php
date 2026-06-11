<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CatchDetail extends Model
{
    protected $guarded = [];

    public function catch()
    {
        return $this->belongsTo(CatchModel::class, 'catch_id');
    }

    public function fish()
    {
        return $this->belongsTo(Fish::class, 'fish_id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class)->withDefault();
    }
}
