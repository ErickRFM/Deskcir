<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'payment_method',
        'card_id',
        'address',
        'city',
        'postal_code',
        'phone',
        'subtotal',
        'shipping_fee',
        'service_fee',
        'discount',
        'wallet_used',
        'delivery_type',
        'pickup_point',
        'delivery_notes',
        'tracking_code',
        'total',
        'status',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'shipping_fee' => 'decimal:2',
        'service_fee' => 'decimal:2',
        'discount' => 'decimal:2',
        'wallet_used' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function card()
    {
        return $this->belongsTo(Card::class);
    }
}
