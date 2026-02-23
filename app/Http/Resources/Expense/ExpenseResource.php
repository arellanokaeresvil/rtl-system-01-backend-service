<?php

namespace App\Http\Resources\Expense;

use Illuminate\Http\Request;
use App\Http\Resources\Batch\BatchResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Expense\ExpenseCategoryResource;

class ExpenseResource extends JsonResource
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
            'expense_category_id' => $this->expense_category_id,
            'expense_date' => $this->expense_date,
            'amount' => $this->amount,
            'reference_no' => $this->reference_no,
            'description' => $this->description,
            'category' => new  ExpenseCategoryResource($this->category),
            'batch' => new BatchResource($this->batch),
            'created_at' =>$this->created_at,
            'updated_at' =>$this->updated_at,
        ];
    }
}
