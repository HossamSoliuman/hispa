<?php

namespace App\Models\Startup;

use App\Traits\BelongsToOwner;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Loan extends Model
{
    use BelongsToOwner, HasFactory;

    protected $table = 'startup_loans';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['date' => 'date', 'first_installment_date' => 'date', 'amount' => 'decimal:2', 'installment_amount' => 'decimal:2'];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(LoanPayment::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function recomputeStatus(): void
    {
        $isPaid = (int) round((float) $this->payments()->sum('amount') * 100)
            >= (int) round((float) $this->amount * 100);

        $this->update([
            'status' => $isPaid ? 'paid' : ($this->status === 'defaulted' ? 'defaulted' : 'active'),
        ]);
    }
}
