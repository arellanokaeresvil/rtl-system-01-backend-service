<?php

namespace App\Http\Resources\Egg;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EggBatchResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
       return [
            'batch_id' => $this->batch_id,
            'batch' => $this->batch->batch_code ?? null,
            'date_collected' => Carbon::parse($this->date_collected)->toFormattedDateString(),
            'jumbo' => $this->jumbo,
            'extra_large' => $this->extra_large,
            'large' => $this->large,
            'medium' => $this->medium,
            'small' => $this->small,
            'extra_small' => $this->extra_small,
            'pewee' => $this->pewee,
            'total' => $this->total,
        ];
    }
}
