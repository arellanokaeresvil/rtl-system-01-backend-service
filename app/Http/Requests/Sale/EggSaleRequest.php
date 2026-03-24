<?php

namespace App\Http\Requests\Sale;

use Illuminate\Foundation\Http\FormRequest;

class EggSaleRequest extends FormRequest
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
            'batch_id' => ['nullable','exists:batches,id',],
            'sold_to' => ['nullable','string','max:255'],
            'unit' => ['required','string','max:255'],
            'grade' => ['required','string','max:255'],
            'notes' => ['nullable','string','max:255'],
            'sold_at' => ['required','date'],
            'quantity' => ['required','numeric','between:0,9999.99'],
            'price_per_unit' => ['required','numeric'],
            'total_amount' => ['required','numeric'],
        ];
    }
}
