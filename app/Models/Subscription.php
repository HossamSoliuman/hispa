<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
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

    /**
     * Cascade a subscription's invoices on delete. The invoices table carries no
     * database foreign key, so removing a subscription would otherwise leave
     * orphaned invoices behind. Deleting a subscription cancels it outright
     * (the model has no SoftDeletes), so its invoices must go with it.
     */
    protected static function booted(): void
    {
        static::deleting(function (Subscription $subscription): void {
            $subscription->invoices()->delete();
        });
    }

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
        return $this->status === 'active' && ! $this->is_suspended && $this->end_date >= Carbon::today();
    }

    public function isExpired()
    {
        return $this->end_date < Carbon::today();
    }

    public function isTrial()
    {
        return $this->status === 'trial';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }
}
