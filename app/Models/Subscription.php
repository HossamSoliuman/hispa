<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Subscription extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'package_id',
        'start_date',
        'end_date',
        'status',
        'is_suspended',
        'trial_ends_at',
        'suspended_at',
        'suspension_reason',
        'renewal_count',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'trial_ends_at' => 'datetime',
        'suspended_at' => 'datetime',
        'is_suspended' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function package()
    {
        return $this->belongsTo(SubscriptionPackage::class, 'package_id');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function isActive()
    {
        return $this->status === 'active' && !$this->is_suspended && $this->end_date >= Carbon::today();
    }

    public function isExpired()
    {
        return $this->end_date < Carbon::today();
    }

    public function isTrial()
    {
        return $this->status === 'trial';
    }
}
