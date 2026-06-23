<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Expense extends Model
{
    use SoftDeletes;

    protected $table = 'expenses';

    protected $fillable = [
        'date',
        'number',
        'notes',
        'owner_id',
        'boat_id',
        'trip_id',
        'total_price',
        'discount_type',
        'discount_value',
        'final_price',
        'status',
        'vendor_id',
        'payment_method_id',
        'category_id',
        'attachment',
        'vat_rate',
    ];

    protected $casts = [
        'total_price' => 'decimal:2',
        'discount_value' => 'decimal:2',
        'final_price' => 'decimal:2',
    ];

    protected $appends = ['category_type', 'expense_type'];

    protected static function booted()
    {
        static::addGlobalScope('owner', function ($query) {
            if (auth()->check() && auth()->user()->role === 'owner') {
                $query->where($query->qualifyColumn('owner_id'), auth()->id());
            }
        });
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function boat()
    {
        return $this->belongsTo(Boat::class);
    }

    public function trip(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Trip::class);
    }

    public function vendor()
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class)->withDefault();
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function details(): HasMany
    {
        return $this->hasMany(Expenseable::class);
    }

    public function getRelatedModelAttribute()
    {
        return null;
    }

    public function getStatusTextAttribute()
    {
        return $this->status === 'paid' ? 'مدفوع' : 'معلق';
    }

    public function getStatusColorAttribute()
    {
        return $this->status === 'paid' ? 'success' : 'warning';
    }

    public function getExpenseTypeAttribute()
    {
        if ($this->category_id) {
            return $this->category->name;
        }

        return 'غير محدد';
    }

    public function getCategoryTypeAttribute()
    {
        return 'general';
    }

    public function getCalculatedDiscountAttribute()
    {
        if (! $this->discount_type || ! $this->discount_value) {
            return 0;
        }

        if ($this->discount_type === 'percentage') {
            return ($this->total_price * $this->discount_value) / 100;
        }

        if ($this->discount_type === 'fixed') {
            return $this->discount_value;
        }

        return 0;
    }

    public function getAttachmentUrlAttribute()
    {
        return $this->attachment
            ? Storage::disk('ocean')->url($this->attachment)
            : null;
    }
}
