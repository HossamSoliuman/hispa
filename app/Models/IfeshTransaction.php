<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IfeshTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'auction_id',
        'item_id',
        'dalal_id',
        'owner_id',
        'final_price',
        'quantity',
        'payment_status',
        'payment_method_id',
        'transaction_date',
    ];

    protected $casts = [
        'final_price' => 'decimal:2',
        'quantity' => 'decimal:2',
        'transaction_date' => 'datetime',
    ];

    // Relationships
    public function auction()
    {
        return $this->belongsTo(IfeshAuction::class, 'auction_id');
    }



    public function dalal()
    {
        return $this->belongsTo(User::class, 'dalal_id');
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_method_id');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('payment_status', 'pending');
    }

    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid');
    }
}
