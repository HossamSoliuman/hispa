<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Port extends Model
{
    protected $table = 'ports';

    protected $fillable = [
        'name',
        'name_en',
        'category_ar',
        'number_big_boats',
        'number_small_boats',
        'number_pleasure_boats',
        'category_en',
        'address',
        'lat',
        'long',
        'status',
        'governorate_id',
    ];

    protected $appends = ['name_ar'];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 1);
    }

    public function governorate()
    {
        return $this->belongsTo(Governorate::class)->withDefault();
    }

    public function trips()
    {
        return $this->hasMany(Trip::class);
    }

    // public function users()
    // {
    //     return $this->hasMany(User::class);
    // }

    public function violations()
    {
        return $this->hasMany(Violation::class);
    }

    public function getNameAttribute()
    {
        if (app()->getLocale() == 'ar') {
            return $this->attributes['name'] ?? $this->attributes['name_en'] ?? __('messages.not_available');
        } else {
            return $this->attributes['name_en'] ?? $this->attributes['name'] ?? __('messages.not_available');
        }
    }

    public function getCategoryAttribute()
    {
        if (app()->getLocale() == 'ar') {
            return $this->attributes['category_ar'] ?? $this->attributes['category_en'] ?? __('messages.not_available');
        } else {
            return $this->attributes['category_en'] ?? $this->attributes['category_ar'] ?? __('messages.not_available');
        }
    }

    public function getNameArAttribute()
    {
        return $this->attributes['name'] ?? __('messages.not_available');
    }

    public function boats()
    {
        return $this->hasMany(Boat::class);
    }

    // In Port model
    public function users()
    {
        return $this->hasManyThrough(
            User::class,
            Governorate::class,
            'id',
            'governorate_id',
            'governorate_id',
            'id'
        );
    }

    public function supervisors()
    {
        return $this->users()->where('role', 'gov');
    }

    public function owners()
    {
        return $this->users()->where('role', 'owner');
    }

    public function counters()
    {
        return $this->users()->where('role', 'counter');
    }

    public function foreigners()
    {
        return $this->users()->whereNot('nationality', 'saudi');
    }
}
