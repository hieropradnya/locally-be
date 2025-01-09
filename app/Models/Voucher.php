<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    protected $fillable = [
        'code',
        'discount_percentage',
        'expiry_date',
        'is_active',
    ];
}
