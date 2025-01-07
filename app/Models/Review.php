<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = ['rating', 'comment', 'image', 'user_id', 'product_id'];

    // relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // relasi ke Product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // relasi ke Order
    // public function order()
    // {
    //     return $this->belongsTo(Order::class);
    // }
}
