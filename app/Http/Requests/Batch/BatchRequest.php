<?php

namespace App\Http\Requests\Batch;

use Illuminate\Foundation\Http\FormRequest;

class BatchRequest extends FormRequest
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
            'supplier_name' => 'nullable|string|max:255',
            'breed' => 'nullable|string|max:255',
            'date_received' => 'required|date',
            'initial_age_weeks' => 'required|integer|min:0',
            'initial_quantity' => 'required|integer|min:1',
            'current_quantity' => 'required|integer|min:0',
            'cost_per_head' => 'required|integer|min:0',
            'daily_feed_per_bird_kg' => 'required|integer|min:0',
            'status' => 'required|string|in:active,peak,sold,culled',
            'notes' => 'nullable|string|max:1000',
        ];
    }
}
