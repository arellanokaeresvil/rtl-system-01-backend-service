<?php

namespace App\Http\Resources\Batch;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BatchOptionResource extends JsonResource
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
            'batch_code' => $this->batch_code,
            'breed' => $this->breed,
            'daily_feed_per_bird_kg' => $this->daily_feed_per_bird_kg,
            'current_quantity' => $this->current_quantity
        ];
    }
}
