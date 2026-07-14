<?php

namespace App\Models;

use App\Enums\CustomFeature;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomFeatureAccess extends Model
{
    /** @use HasFactory<\Database\Factories\CustomFeatureAccessFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'feature',
        'status',
        'paused_at',
        'granted_by_admin_id',
    ];

    protected function casts(): array
    {
        return [
            'feature' => CustomFeature::class,
            'paused_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function grantedByAdmin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'granted_by_admin_id');
    }

    public function scopeForFeature(Builder $query, CustomFeature $feature): Builder
    {
        return $query->where('feature', $feature->value);
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}
