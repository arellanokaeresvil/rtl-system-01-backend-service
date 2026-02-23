<?php

namespace App\Http\Resources\Expense;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExpenseCategoryResource extends JsonResource
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
            'name' => $this->name,
            'is_batch_specific' => $this->is_batch_specific,
            // 'expenses' => ExpenseResource::collection($this->expenses),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
