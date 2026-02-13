<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PartOrder extends Model
{
    protected $fillable = [
        'order_code',
        'part_id',
        'quantity',
        'unit_price',
        'total_price',
        'customer_name',
        'customer_email',
        'customer_phone',
        'shipping_address',
        'shipping_city',
        'shipping_postal_code',
        'shipping_courier',
        'shipping_service',
        'shipping_cost',
        'shipping_details',
        'payment_method',
        'payment_reference',
        'payment_details',
        'status',
        'paid_at',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'shipping_details' => 'array',
        'payment_details' => 'array',
        'paid_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->order_code)) {
                $order->order_code = 'PART-' . strtoupper(Str::random(8));
            }
        });
    }

    public function part()
    {
        return $this->belongsTo(Part::class);
    }

    public function getTotalAmountAttribute()
    {
        return $this->total_price + $this->shipping_cost;
    }
}
