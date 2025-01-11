<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostProduct extends Model
{
    protected $fillable = [
        'post_id',
        'product_id',
    ];

    /**
     * Relasi ke Post.
     */
    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    /**
     * Relasi ke Product.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
