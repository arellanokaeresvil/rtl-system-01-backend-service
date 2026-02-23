<?php

namespace App\Http\Requests\User;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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
        $userId = $this->route('user'); // null on create, user ID on update
        $isUpdate = $userId !== null; // determine if this is an update request

        return [
            'name' => ['required', 'string', 'max:255', 'min:2'],
            'email' => ['required', 'email', 'string', 'max:255', 
                 Rule::unique('users')->ignore( $userId)],
            'password' => $isUpdate
            ? ['nullable', 'string', 'min:6', 'max:16', 'confirmed']
            : ['required', 'string', 'min:6', 'max:16', 'confirmed']
        ];
    }
}
