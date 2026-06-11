<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReturnDetail extends Model
{
    protected $guarded = [];

    protected $table = 'return_details';

    public function fish()
    {
        return $this->belongsTo(Fish::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class)->withDefault();
    }

    public function return()
    {
        return $this->belongsTo(ReturnModel::class, 'return_id');
    }
}
