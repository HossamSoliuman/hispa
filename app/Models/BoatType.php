<?php

namespace App\Models;

use App\Traits\BelongsToOwner;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BoatType extends Model
{
    use BelongsToOwner;

    protected $fillable = ['name_ar', 'name_en', 'status', 'owner_id'];

    protected $appends = ['name'];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 1);
    }

    public function boat(): BelongsTo
    {
        return $this->belongsTo(Boat::class, 'boat_id');
    }

    public function getNameAttribute()
    {
        if (app()->getLocale() == 'ar') {
            return $this->attributes['name_ar'] ?? $this->attributes['name_en'] ?? __('messages.not_available');
        } else {
            return $this->attributes['name_en'] ?? $this->attributes['name'] ?? __('messages.not_available');
        }
    }

    public function boats()
    {
        return $this->hasMany(Boat::class, 'boat_type_id');
    }

    public function ports()
    {
        return $this->belongsToMany(Port::class, 'port_boat_types')
            ->withPivot('max')
            ->withTimestamps();
    }
}
