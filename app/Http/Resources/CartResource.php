<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'quantity' => $this->quantity,
            'variant' => $this->productVariant->variant,
            'product_name' => $this->productVariant->product->name,
            'product_thumbnail' => url('storage/productImages/' . $this->productVariant->product->thumbnail),
            'product_price' => $this->productVariant->product->price,
            'brand_name' => $this->productVariant->product->seller->brand_name,
            'seller_logo' => url('storage/seller/logos/' . $this->productVariant->product->seller->logo),
        ];
    }
}
