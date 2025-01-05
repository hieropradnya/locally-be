<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Seller extends Model
{
    protected $primaryKey = 'id';
    public $incrementing = false;


    protected $fillable = [
        'id',
        'description',
        'brand_name',
        'logo',
        'banner',
        'status',
    ];

    // relasi ke model User
    public function user()
    {
        return $this->belongsTo(User::class, 'id', 'id');
    }
}
