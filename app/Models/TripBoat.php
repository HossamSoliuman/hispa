<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class TripBoat extends Model
{
    protected $table = 'trip_boats';

    protected $fillable = [
        'trip_id',
        'boat_id',
        'boat_name',
        'boat_number',
        'boat_color',
        'boat_length',
        'boat_width',
        'crew_count',
    ];

    protected function casts(): array
    {
        return [
            'boat_length' => 'float',
            'boat_width' => 'float',
            'crew_count' => 'integer',
        ];
    }

    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class);
    }

    public function boat(): BelongsTo
    {
        return $this->belongsTo(Boat::class);
    }

    /**
     * Captains assigned to this boat for this trip (snapshot, picked per trip).
     */
    public function captains(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'trip_boat_captains', 'trip_boat_id', 'captain_id')
            ->withTimestamps();
    }
}
