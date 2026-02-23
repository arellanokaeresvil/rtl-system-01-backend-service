<?php

namespace App\Http\Resources\Mortality;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MortalityResource extends JsonResource
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
            'batch' => $this->batch ?? null,
            'date' => $this->date,
            'count' => $this->count,
            'cause' => $this->cause,
            'notes' => $this->notes,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
