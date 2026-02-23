<?php

namespace App\Http\Requests\Feed;

use Illuminate\Foundation\Http\FormRequest;

class FeedRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'date_manufactured' => 'required|date',
            'quantity_kg' => 'required|numeric|min:0',
            'cost_per_kg' => 'required|numeric|min:0',
            'remaining_kg' => 'nullable|numeric|min:0',
            'supplier' => 'required|string|max:255',
        ];
    }
}
