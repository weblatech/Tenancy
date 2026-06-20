<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'customer_name',
        'customer_phone',
        'customer_address',
        'customer_city',
        'cart_items',
        'subtotal',
        'shipping_fee',
        'total',
        'status',
        'ip_address',
        'ip_country',
        'ip_city',
        'latitude',
        'longitude',
        'isp',
        'customer_id',
        'payment_method',
        'cod_advance_required',
        'cod_advance_paid',
    ];

    protected $casts = [
        'cart_items' => 'array',
        'cod_advance_paid' => 'boolean',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
