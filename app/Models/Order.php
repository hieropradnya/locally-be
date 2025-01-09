<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'status_order_id',
        'message',
        'total_price',
        'is_reviewed',
        'seller_id',
        'user_id',
        'address_id',
        'pricing_detail_id',
        'voucher_id',
    ];

    public function seller()
    {
        return $this->belongsTo(Seller::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    public function priceDetail()
    {
        return $this->belongsTo(PriceDetail::class);
    }

    public function voucher()
    {
        return $this->belongsTo(Voucher::class);
    }
}
