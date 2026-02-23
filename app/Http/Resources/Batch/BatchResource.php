<?php

namespace App\Http\Resources\Batch;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BatchResource extends JsonResource
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
            'supplier_name' => $this->supplier_name,
            'date_received' => $this->date_received,
            'initial_age_weeks' => $this->initial_age_weeks,
            'current_age_weeks' => $this->CurrentAgeWeeks,
            'initial_quantity' => $this->initial_quantity,
            'current_quantity' => $this->current_quantity,
            'cost_per_head' => intval($this->cost_per_head),
            'daily_feed_per_bird_kg' => intval($this->daily_feed_per_bird_kg),
            'status' => $this->status,
            'notes' => $this->notes,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
