<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $fillable = ['quantity', 'user_id', 'product_variant_id'];

    // relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // relasi ke ProductVariant
    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class);
    }
}
