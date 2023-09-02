<?php

namespace App\Http\Resources;

use App\Http\Resources\ProductResource;
use App\Http\Resources\ProfileResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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
            'total' => $this->total,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'user' => new ProfileResource($this->whenLoaded('user')),
            'product' => new ProductResource($this->whenLoaded('product')),
        ];
    }
}
