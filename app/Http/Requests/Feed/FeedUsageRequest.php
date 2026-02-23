<?php

namespace App\Http\Requests\Feed;

use Illuminate\Foundation\Http\FormRequest;

class FeedUsageRequest extends FormRequest
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
            'feed_id' => 'required|exists:feeds,id',
            'batch_id' => 'required|exists:batches,id',
            'used_at' => 'required|date',
            'quantity_kg' => 'required|numeric|min:0',
            'remarks' => 'nullable|string|max:255',
        ];
    }
}
