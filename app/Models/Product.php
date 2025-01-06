<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{

    protected $fillable = [
        'name',
        'description',
        'price',
        'stock',
        'seller_id',
    ];

    // relasi ke Seller
    public function seller()
    {
        return $this->belongsTo(Seller::class, 'seller_id');
    }

    // relasi ke ProductImage
    public function images()
    {
        return $this->hasMany(ProductImage::class, 'product_id');
    }
}