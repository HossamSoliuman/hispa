<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Violation extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_number',
        'violation_type',
        'violation_date',
        'violation_time',
        'description',
        'region_id',
        'status',
        'location',
        'reported_by',
        'fine_amount',
        'trip_id',
        'trip_id',
        'governorate_id',
        'port_id',
    ];

    public function getStatusLabelAttribute()
    {
        return $this->status == 1 ? 'مخالفة جديدة' : 'مخالفة مسددة';
    }

    public function region()
    {
        return $this->belongsTo(Region::class, 'region_id');
    }

    public function trip()
    {
        return $this->belongsTo(Trip::class, 'trip_id');
    }

    public function governorate()
    {
        return $this->belongsTo(Governorate::class);
    }

    public function port()
    {
        return $this->belongsTo(Port::class);
    }
}
