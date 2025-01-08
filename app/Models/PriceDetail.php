<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PriceDetail extends Model
{
    protected $fillable = [
        'product_subtotal',
        'shipping_cost',
        'service_fee',
        'discount',
    ];
}
