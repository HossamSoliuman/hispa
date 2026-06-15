<?php

namespace App\Models;

use App\Observers\SaleObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[ObservedBy(SaleObserver::class)]

class Sale extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'number',
        'seller_type',
        'seller_id',
        'payment_status',
        'status',
        'trip_id',
        'customer_id',
        'customer_name',
        'payment_method_id',
        'payment_method',
        'commission_rate',
        'commission_amount',
        'labor_rate',
        'labor_amount',
        'total_price',
        'invoice_sent_at',
        'invoice_sent_note',
        'net_owner_amount',
        'remaining_total',
        'sale_datetime',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'integer',
            'invoice_sent_at' => 'datetime',
            'sale_datetime' => 'datetime',
        ];
    }

    public static function statusText($status)
    {
        return match ($status) {
            1 => __('admin.sales.in_progress'),
            2 => __('admin.sales.completed'),
            default => __('admin.sales.unknown'),
        };
    }

    public static function paymentStatusText($status)
    {
        return match ($status) {
            'unpaid' => 'غير مدفوع',
            'partially_paid' => 'مدفوع جزئيا',
            'paid' => 'مدفوع',
            default => __('admin.sales.unknown'),
        };
    }

    public function trip()
    {
        return $this->belongsTo(Trip::class)->withDefault();
    }

    public function details()
    {
        return $this->hasMany(SaleDetail::class, 'sale_id');
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id')->withDefault();
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class)->withDefault();
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class)->withDefault();
    }
}
