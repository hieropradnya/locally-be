<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'name'          => $this->name,
            'thumbnail'     => url('storage/productImages/' . $this->thumbnail),
            'description'   => $this->description,
            'price'         => $this->price,
            'stock'         => $this->stock,
            'images'        => ImageResource::collection($this->images),
            'variants'      => VariantResource::collection($this->variants),
            'category'      => new CategoryResource($this->category),
            'seller'        => new SellerResource($this->seller),
        ];
    }
}
