<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'status_order' => $this->statusOrder->name, // Menampilkan status pesanan
            'total_price' => $this->total_price,
            'is_reviewed' => $this->is_reviewed,
            'brand_name' => $this->seller->brand_name,
            'seller_logo' => url('storage/profileImages/' .  $this->seller->logo),
            'order_details' => $this->orderDetail->map(function ($orderDetail) {
                return [
                    'quantity' => $orderDetail->quantity,
                    'product_price' => $orderDetail->product_price,
                    'variant' => $orderDetail->productVariant->variant, // Menampilkan variant produk
                    'product_name' => $orderDetail->productVariant->product->name, // Menampilkan nama produk
                    'product_thumbnail' => url('storage/productImages' . $orderDetail->productVariant->product->thumbnail), // Menampilkan thumbnail produk
                ];
            }),
        ];
    }
}
