<?php

namespace App\Http\Requests\Egg;

use Illuminate\Foundation\Http\FormRequest;

class EggRequest extends FormRequest
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
            'date_collected' => ['required','date'],
            'weight_grams' => ['nullable','numeric','between:0,9999.99'],
            'grade' => ['nullable','in:S,M,L,XL'],
            'status' => ['nullable','in:good,cracked,reject'],
            'source' => ['nullable','in:manual,esp32'],
            'device_id' => ['nullable','string','max:255'],
        ];
    }
}
