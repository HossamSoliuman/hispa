<?php

namespace App\Models;

use App\Traits\BelongsToOwner;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class PaymentMethod extends Model
{
    use BelongsToOwner, SoftDeletes;

    protected $fillable = [
        'name',
        'name_en',
        'icon',
        'status',
        'owner_id',
    ];

    protected $appends = ['name_ar'];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 1);
    }

    public function getIconAttribute($key)
    {

        // check if request is from laravel nova
        if ($key == '' || is_null($key)) {
            return asset('storage/default.jpg');
        } else {
            // return Storage::disk('ocean')->url($key);
            return Storage::url($key);
        }

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

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }
}
