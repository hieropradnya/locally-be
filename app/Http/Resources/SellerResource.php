<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SellerResource extends JsonResource
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
            'brand_name'    => $this->brand_name,
            'description'   => $this->description,
            'logo'          => url('storage/seller/logos/' . $this->logo),
            'banner'        => url('storage/seller/banners/' . $this->banner),
        ];
    }
}
