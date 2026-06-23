<?php

namespace App\Models;

use App\Traits\BelongsToOwner;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Region extends Model
{
    use BelongsToOwner;

    protected $table = 'regions';

    protected $fillable = ['id', 'name', 'name_en', 'status', 'owner_id'];

    protected $appends = ['name_ar'];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 1);
    }

    public function governorates()
    {
        return $this->hasMany(Governorate::class);
    }

    public function ports(): HasManyThrough
    {
        return $this->hasManyThrough(
            Port::class,
            Governorate::class,
            'region_id',
            'governorate_id',
            'id',
            'id'
        );
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function getNameAttribute()
    {
        if (app()->getLocale() == 'ar') {
            return $this->attributes['name'] ?? $this->attributes['name_en'] ?? __('messages.not_available');
        } else {
            return $this->attributes['name_en'] ?? $this->attributes['name'] ?? __('messages.not_available');
        }
    }

    public function getNameArAttribute()
    {
        return $this->attributes['name'] ?? __('messages.not_available');
    }
}
