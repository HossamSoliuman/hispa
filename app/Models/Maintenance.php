<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Maintenance extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'date',
        'category_id',
        'boat_id',
        'owner_id',
        'estimated_cost',
        'description',
        'technician',
    ];

    protected static function booted()
    {
        static::addGlobalScope('owner', function ($query) {
            if (auth()->check() && auth()->user()->role === 'owner') {
                $query->where('owner_id', auth()->id());
            }
        });
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function boat()
    {
        return $this->belongsTo(Boat::class);
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
}
