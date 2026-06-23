<?php

namespace App\Models;

use App\Traits\BelongsToOwner;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Governorate extends Model
{
    use BelongsToOwner;

    protected $table = 'governorates';

    protected $fillable = ['id', 'name', 'name_en', 'region_id', 'status', 'owner_id'];

    protected $appends = ['name_ar'];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 1);
    }

    public function region()
    {
        return $this->belongsTo(Region::class)->withDefault();
    }

    //    public function cities()
    //    {
    //        return $this->hasMany(City::class);
    //    }
    public function ports()
    {
        return $this->hasMany(Port::class);
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
