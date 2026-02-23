<?php

namespace App\Http\Resources\Expense;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ExpenseCategoryOptionResource extends ResourceCollection
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request)
    {
        return $this->collection->map(function ($category) {
            return [
                'id' => $category->id,
                'name' => $category->name,
                'is_batch_specific' => $category->is_batch_specific,
            ];
        });
    }
}
