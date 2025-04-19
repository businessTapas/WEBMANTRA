<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'order_number',
        'user_id',
        'sub_total',
        'shipping_id',
        'coupon',
        'gst',
        'delivery',
        'total_amount',
        'quantity',
        'payment_method',
        'payment_status',
        'notes',
        'status',
        'tracking_id',
    ];

    public function details()
    {
        return $this->hasMany(OrderDetail::class, 'order_id', 'id');
    }
    public function shippingAdd()
    {
        return $this->hasOne(ShippingDetail::class, 'order_id', 'id');
    }
    public function cart_info()
    {
        return $this->hasMany('App\Models\Cart', 'order_id', 'id');
    }
    public static function getAllOrder($id)
    {
        return Order::with('cart_info')->find($id);
    }
    public static function countActiveOrder()
    {
        return self::whereNotIn('status', ['delivered', 'cancel'])->count();
    }
    public function cart()
    {
        return $this->hasMany(Cart::class);
    }

    public function shipping()
    {
        return $this->belongsTo(Shipping::class, 'shipping_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
