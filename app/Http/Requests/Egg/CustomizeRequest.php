<?php

namespace App\Http\Requests\Egg;

use Illuminate\Foundation\Http\FormRequest;

class CustomizeRequest extends FormRequest
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
            'items' => ['required', 'array'],

            'items.*.batch_id' => ['required', 'exists:batches,id'],
            'items.*.date_collected' => ['required', 'date'],
            'items.*.weight_grams' => ['nullable', 'numeric', 'between:0,9999.99'],
            'items.*.grade' => ['nullable', 'in:P,XS,S,M,L,XL,J'],
            'items.*.total' => ['nullable', 'numeric', 'between:0,9999.99'],
        ];
    }

    public function messages()
        {
            return [
                'items.*.batch_id.required' => 'Batch is required.',
                'items.*.batch_id.exists' => 'Selected batch does not exist.',
                'items.*.grade.in' => 'Invalid egg grade.',
            ];
        }
}
