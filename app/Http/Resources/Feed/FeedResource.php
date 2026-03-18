<?php

namespace App\Http\Resources\Feed;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FeedResource extends JsonResource
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
            'feed_code' => $this->feed_code,
            'name' => $this->name,
            'type' => $this->type,
            'date_manufactured' => $this->date_manufactured,
            'quantity_kg' => $this->quantity_kg,
            'remaining_kg' => $this->remaining_kg,
            'cost_per_kg' => number_format($this->cost_per_kg, 2),
            'total_cost' => number_format($this->quantity_kg * $this->cost_per_kg, 2),
            'supplier' => $this->supplier,

        ];
    }
}
