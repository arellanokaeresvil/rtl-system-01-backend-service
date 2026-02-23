<?php

namespace App\Http\Requests\Sale;

use Illuminate\Foundation\Http\FormRequest;

class BirdSaleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
         return [
            'batch_id' => ['required','exists:batches,id',],
            'sold_to' => ['nullable','string','max:255'],
            'notes' => ['nullable','string','max:255'],
            'sold_at' => ['required','date'],
            'count' => ['required','numeric','between:0,9999.99'],
            'price_per_bird' => ['required','numeric'],
            'total_amount' => ['required','numeric'],
        ];
    }
}
