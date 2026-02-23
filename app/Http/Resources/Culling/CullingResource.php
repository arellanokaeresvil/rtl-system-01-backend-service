<?php

namespace App\Http\Resources\Culling;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CullingResource extends JsonResource
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
            'reason' => $this->cause,
            'sale_amount' => $this->sale_amount,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
