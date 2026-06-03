<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    protected $guarded = [];

    public function boat()
    {
        return $this->belongsTo(Boat::class);
    }

    public function depreciations()
    {
        return $this->hasMany(AssetDepreciation::class, 'asset_id');
    }

    public function latestDepreciation()
    {
        return $this->hasOne(AssetDepreciation::class)->latestOfMany('year');
    }
}
