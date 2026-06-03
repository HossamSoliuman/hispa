<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Boat extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_ar',
        'name_en',
        'number',
        'status',
        'length',
        'width',
        'color',
        'owner_id',
        'type',
        'license_number',
        'license_region_id',
        'license_date',
        'license_date_expire',
        'body_number',
        'body_type',
        'callsign_number',
        'serial_number',
        'engine_status',
        'engine_type',
        'engine_power',
        'crew_number',
        'payload',
        'region_id',
        'governorate_id',
        'port_id',
    ];

    protected $appends = ['name'];

    protected $casts = [

        'payload' => 'decimal:2',
        'status' => 'integer',
        'engine_status' => 'integer',
        'crew_number' => 'integer',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 1);
    }

    /**
     * License Region relationship
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function asset()
    {
        return $this->hasOne(Asset::class, 'boat_id');
    }

    public function crews()
    {
        return $this->hasMany(User::class)->where('role', 'crew');
    }

    public function stocks()
    {
        return $this->hasMany(FishQuantityStock::class);
    }

    public function licenseRegion()
    {
        return $this->belongsTo(Region::class, 'license_region_id');
    }

    /**
     * Region relationship
     */
    public function region()
    {
        return $this->belongsTo(Region::class, 'region_id');
    }

    /**
     * Governorate relationship
     */
    public function governorate()
    {
        return $this->belongsTo(Governorate::class, 'governorate_id');
    }

    public function captain()
    {
        return $this->hasOne(User::class, 'boat_id')->where('role', 'captain');
    }

    /**
     * Port relationship
     */
    public function port()
    {
        return $this->belongsTo(Port::class, 'port_id');
    }

    public function trips()
    {
        return $this->hasMany(Trip::class);
    }

    public function getNameAttribute()
    {
        $locale = app()->getLocale();

        if ($locale === 'en') {
            return $this->attributes['name_en'] ?? '';
        }

        return $this->attributes['name_ar'] ?? '';
    }

    public function boat_type(): BelongsTo
    {
        return $this->belongsTo(BoatType::class, 'boat_type_id');
    }


    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function maintenances()
    {
        return $this->hasMany(Maintenance::class);
    }

    public function inspections()
    {
        return $this->hasMany(Inspection::class);
    }
}
