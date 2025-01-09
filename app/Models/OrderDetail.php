<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    // Kolom yang dapat diisi (mass assignable)
    protected $fillable = [
        'quantity',
        'product_price',
        'product_variant_id',
        'order_id',
    ];

    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
