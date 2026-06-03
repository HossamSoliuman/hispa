<?php

namespace App\Models;

use App\Observers\TripObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

#[ObservedBy(TripObserver::class)]
class Trip extends Model
{
    use SoftDeletes;

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

    protected $casts = [
        'boat_length' => 'float',
        'boat_width' => 'float',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'departure_time' => 'datetime:H:i',
        'return_time' => 'datetime:H:i',
        'actual_start_datetime' => 'datetime',
        'actual_end_datetime' => 'datetime',
    ];

    /** Status ID => Bootstrap badge color (e.g. primary, success). */
    public static function statusBadgeColors(): array
    {
        return [
            1 => 'primary',
            2 => 'info',
            3 => 'danger',
            4 => 'secondary',
            5 => 'warning',
            6 => 'warning',
            7 => 'success',
            8 => 'success',
        ];
    }

    public function getStatusBadgeColorAttribute(): string
    {
        return self::statusBadgeColors()[$this->status ?? 1] ?? 'dark';
    }

    /**
     * Returns the translated status label for a given status ID (uses admin.trip_statuses).
     */
    public static function getStatusLabelById(?int $status): string
    {
        if ($status === null || $status < 1) {
            return '—';
        }
        $labels = __('admin.trip_statuses');
        if (is_array($labels) && isset($labels[$status])) {
            $label = $labels[$status];
            return is_string($label) ? $label : (string) $status;
        }
        return (string) $status;
    }

    public function getStatusLabelAttribute(): string
    {
        return self::getStatusLabelById($this->status);
    }

    public function getLicenseAttachmentAttribute($key)
    {

        // check if request is from laravel nova
        if ($key == '' || is_null($key)) {
            return asset('uploads/default.jpg');
        } else {
            // return Storage::disk('ocean')->url($key);
            return Storage::url($key);
        }
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

        return $days . ' ' . $dayLabel;
    }

    // علاقات المستخدمين
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

    // العلاقات الجغرافية
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

    public static function allowedStatusUpdatesByRole(): array
    {
        return [
            'captain' => [2, 3, 4],
            'counter' => [5, 6],  // progress_count, counter_done
            'owner' => [7, 8],  // ready_to_sell, sent_to_broker
            //            'dalal'   => [9, 10, 11], // awaiting_broker, selling, completed
        ];
    }

    public static function isValidTransition($role, $current, $next): bool
    {
        $map = [
            'captain' => [
                1 => [2, 3],    // new -> in_progress or cancel
                2 => [4, 3],    // in_progress -> complete or cancel
            ],
            'counter' => [
                4 => [5, 6],    // captain_done -> awaiting_count or counting
                5 => [6],       // awaiting_count -> counting
                6 => [7],       // counting -> ready_to_sell
            ],
            'owner' => [
                6 => [7],       // ready_to_sell -> sent_to_broker
                7 => [8],       // ready_to_sell -> sent_to_broker
            ],

        ];

        return in_array($next, $map[$role][$current] ?? []);
    }

    public static function statuses()
    {
        $statusKeys = __('trips.status_keys');
        
        // If translation returns a string (key not found), return default array
        if (!is_array($statusKeys)) {
            return [1, 2, 3, 4, 5, 6, 7, 8];
        }
        
        return $statusKeys;
    }

    public static function getStatusLabels()
    {
        $labels = __('trips.status_labels');
        
        // If translation returns a string (key not found), return default labels
        if (!is_array($labels)) {
            return [
                1 => __('admin.trip_statuses.1', [], 'New'),
                2 => __('admin.trip_statuses.2', [], 'In Progress'),
                3 => __('admin.trip_statuses.3', [], 'Cancelled'),
                4 => __('admin.trip_statuses.4', [], 'Finished by Captain'),
                5 => __('admin.trip_statuses.5', [], 'Counting'),
                6 => __('admin.trip_statuses.6', [], 'Count Completed'),
                7 => __('admin.trip_statuses.7', [], 'Ready for Sale'),
                8 => __('admin.trip_statuses.8', [], 'Completed'),
            ];
        }
        
        return $labels;
    }

    public function getStatusNameAttribute(): string
    {
        return self::getStatusLabelById($this->status);
    }

    public function getStatusLabelForRole(string $role): string
    {
        $statusKey = self::statuses()[$this->status] ?? 'unknown';

        return __('trips.roles.' . $role . '.' . $statusKey) ?? $statusKey;
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
