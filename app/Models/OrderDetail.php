<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'item',
        'quantity',
        'price',
        'sub_total',
        'gst',
        'total',
    ];

    public function prodDetail()
    {
        return $this->hasOne(Product::class, 'id', 'product_id');
    }
}
