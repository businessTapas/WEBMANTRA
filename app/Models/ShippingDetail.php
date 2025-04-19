<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'fname',
        'lname',
        'phone',
        'email',
        'address',
        'country',
        'state',
        'city',
        'pin',
    ];
}
