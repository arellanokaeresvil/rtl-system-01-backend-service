<?php

namespace App\Http\Requests\Mortality;

use Illuminate\Foundation\Http\FormRequest;

class MortalityRequest extends FormRequest
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
            'batch_id' => 'required|exists:batches,id',
            'date' => 'required|date',
            'count' => 'required|integer|min:1',
            'cause' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:500',
        ];
    }
}
