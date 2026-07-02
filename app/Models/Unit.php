<?php

namespace App\Models;

use App\Traits\BelongsToOwner;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use BelongsToOwner;

    protected $fillable = [
        'name_ar',
        'name_en',
        'is_default',
        'status',
        'owner_id',
    ];

    protected $appends = ['name'];

    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
            'status' => 'boolean',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 1);
    }

    public function getNameAttribute(): string
    {
        if (app()->getLocale() === 'en') {
            return $this->name_en ?: $this->name_ar;
        }

        return $this->name_ar ?: ($this->name_en ?? '');
    }

    public static function defaultId(): ?int
    {
        return static::where('is_default', 1)->value('id')
            ?? static::query()->value('id');
    }
}
