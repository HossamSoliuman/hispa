<?php

namespace App\Models;

use App\Enums\TripStatus;
use App\Observers\TripObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

#[ObservedBy(TripObserver::class)]
class Trip extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'trips';

    protected $fillable = [
        'id',
        'name',
        'name_en',
        'number',
        'license_number',
        'status',
        'cancel_reason',
        'permit_type',
        'owner_id',
        'crew_count',
        'captain_id',
        'boat_name',
        'boat_number',
        'boat_color',
        'boat_length',
        'boat_width',
        'departure_time',
        'return_time',
        'start_date',
        'end_date',
        'actual_start_datetime',
        'actual_end_datetime',
        'region_id',
        'governorate_id',
        'city_id',
        'port_id',
        'departure_port',
        'return_port',
        'counter_id',
        'dalal_id',
        'license_attachment',
        'notes',
        'boat_id',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'boat_length' => 'float',
            'boat_width' => 'float',
            'start_date' => 'datetime',
            'end_date' => 'datetime',
            'departure_time' => 'datetime:H:i',
            'return_time' => 'datetime:H:i',
            'actual_start_datetime' => 'datetime',
            'actual_end_datetime' => 'datetime',
            'status' => TripStatus::class,
        ];
    }

    public function getLicenseAttachmentAttribute($key): string
    {
        if ($key == '' || is_null($key)) {
            return asset('uploads/default.jpg');
        }

        return Storage::url($key);
    }

    public function boat()
    {
        return $this->belongsTo(Boat::class, 'boat_id');
    }

    public function scopeCaptainId(Builder $query): Builder
    {
        return $query->where('captain_id', request()->user()->id);
    }

    public function scopeCounterId(Builder $query): Builder
    {
        return $query->where('counter_id', request()->user()->id);
    }

    public function scopeOwnerId(Builder $query): Builder
    {
        return $query->where('owner_id', request()->user()->id);
    }

    public function scopeDalalId(Builder $query): Builder
    {
        return $query->where('dalal_id', request()->user()->id);
    }

    public function getDurationTextAttribute(): ?string
    {
        if (! $this->start_date || ! $this->end_date) {
            return null;
        }

        $days = Carbon::parse($this->start_date)->diffInDays(Carbon::parse($this->end_date)) + 1;
        $dayLabel = $days == 1 ? __('trips.duration.day_singular') : __('trips.duration.day_plural');

        return $days.' '.$dayLabel;
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function captain()
    {
        return $this->belongsTo(User::class, 'captain_id');
    }

    public function counter()
    {
        return $this->belongsTo(User::class, 'counter_id');
    }

    public function dalal()
    {
        return $this->belongsTo(User::class, 'dalal_id');
    }

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function governorate()
    {
        return $this->belongsTo(Governorate::class);
    }

    public function port()
    {
        return $this->belongsTo(Port::class);
    }

    public function fishQuantityStocks()
    {
        return $this->hasMany(FishQuantityStock::class);
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function expenses(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function getNameAttribute()
    {
        if (app()->getLocale() == 'ar') {
            return $this->attributes['name'] ?? __('messages.not_available');
        } else {
            return $this->attributes['name_en'] ?? __('messages.not_available');
        }
    }

    public function getPermitTypeAttribute($value)
    {
        $map = __('trips.permit_types');

        return $map[$value] ?? $value;
    }

    public function getPermitTypeKeyAttribute()
    {
        return $this->attributes['permit_type'];
    }

    public function catches()
    {
        return $this->hasOne(CatchModel::class, 'trip_id');
    }
}
