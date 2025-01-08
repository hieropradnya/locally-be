<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $fillable = [
        'recipient_name',
        'phone',
        'address',
        'postal_code',
        'city_id',
        'province_id',
        'user_id',
    ];

    /**
     * relasi ke user.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function city()
    {
        return $this->belongsTo(City::class);
    }
    public function province()
    {
        return $this->belongsTo(Province::class);
    }
}
