<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payroll extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'period_from' => 'date',
            'period_to' => 'date',
        ];
    }

    public function boat(): BelongsTo
    {
        return $this->belongsTo(Boat::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function details(): HasMany
    {
        return $this->hasMany(PayrollDetail::class);
    }
}
