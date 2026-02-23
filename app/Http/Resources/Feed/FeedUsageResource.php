<?php

namespace App\Http\Resources\Feed;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FeedUsageResource extends JsonResource
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
           'feed' => $this->feed ?? null,
           'feed_id' => $this->feed_id,
           'batch' => $this->batch ?? null,
           'batch_id' => $this->batch_id,
           'used_at' => $this->used_at,
           'quantity_kg' => $this->quantity_kg,
           'source' => $this->source,
           'remarks' => $this->remarks,
           'created_at' => $this->created_at,
           'updated_at' => $this->updated_at,
       ];
    }
}
