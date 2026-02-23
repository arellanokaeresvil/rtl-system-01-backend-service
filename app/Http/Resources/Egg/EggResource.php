<?php

namespace App\Http\Resources\Egg;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EggResource extends JsonResource
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
            'batch_id' => $this->batch_id,
            'batch' => $this->batch->batch_code ?? null,
            'date_collected' => $this->date_collected,
            'weight_grams' => $this->weight_grams,
            'grade' => $this->grade,
            'status' => $this->status,
            'source' => $this->source,
            'device_id' => $this->device_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
