<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PayrollDetailsModel extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'is_paid' => 'boolean',
            'paid_at' => 'datetime',
            'paid_amount' => 'decimal:2',
        ];
    }

    /**
     * Every payroll line for one person under one owner, ordered oldest period
     * first — the source for the special payroll (dues) statement report.
     *
     * @return Collection<int, PayrollDetailsModel>
     */
    public static function statementFor(int $userId, int $ownerId): Collection
    {
        return static::query()
            ->where('user_id', $userId)
            ->whereHas('payroll', fn ($q) => $q->where('owner_id', $ownerId))
            ->with('payroll')
            ->get()
            ->sortBy(fn ($detail) => sprintf('%04d-%02d', (int) ($detail->payroll?->year ?? 0), (int) ($detail->payroll?->month ?? 0)))
            ->values();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function payroll(): BelongsTo
    {
        return $this->belongsTo(PayrollModel::class, 'payroll_id');
    }
}
