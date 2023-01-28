<?php

namespace App\Http\Resources\Cart\v1;

use Illuminate\Http\Resources\Json\JsonResource;

class CartItemResource extends JsonResource
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
            'name' => $this->product->name,
            'quantity' => $this->quantity,
            'price' => $this->price,
            'total' => $this->total
        ];
    }
}
