<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'email',
        'status',
        'notes',
        'owner_id',
        'dalal_id',
        'type',
        'region_id',
        'city_id',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 1);
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function dalal()
    {
        return $this->belongsTo(User::class, 'dalal_id');
    }

    public function region()
    {
        return $this->belongsTo(\App\Models\Region::class, 'region_id')->withDefault();
    }

    public function city()
    {
        return $this->belongsTo(\App\Models\City::class, 'city_id')->withDefault();
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }
}
