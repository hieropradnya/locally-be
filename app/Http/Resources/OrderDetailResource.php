<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderDetailResource extends JsonResource
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
            'status_order_id' => $this->status_order_id,
            'message' => $this->message,
            'total_price' => $this->total_price,
            'is_reviewed' => $this->is_reviewed,
            'seller_id' => $this->seller_id,
            'user_id' => $this->user_id,
            'address_id' => $this->address_id,
            'price_detail_id' => $this->price_detail_id,
            'voucher_id' => $this->voucher_id,

            // Mengambil relasi
            'status_order' => $this->statusOrder,
            'address' => $this->address,
            'seller' => $this->seller,
            'price_detail' => $this->priceDetail,

            // Mengambil order details termasuk product dan product_variant
            'order_details' => $this->orderDetail->map(function ($orderDetail) {
                return [
                    'id' => $orderDetail->id,
                    'quantity' => $orderDetail->quantity,
                    'product_price' => $orderDetail->product_price,
                    'product_variant' => $orderDetail->productVariant ? $orderDetail->productVariant->only(['id', 'variant', 'stock']) : null,
                    'product' => $orderDetail->productVariant && $orderDetail->productVariant->product
                        ? $orderDetail->productVariant->product->only(['id', 'name', 'thumbnail', 'description', 'price'])
                        : null
                ];
            })
        ];
    }
}
