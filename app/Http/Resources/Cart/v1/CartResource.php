<?php

namespace App\Http\Resources\Cart\v1;

use App\Http\Resources\Status\v1\StatusResource;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'total' => $this->total,
            'cart_items' => new CartItemCollection($this->cart_items),
            'status' => new StatusResource($this->status)
        ];
    }
}
