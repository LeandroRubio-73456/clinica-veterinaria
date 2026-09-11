<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreVeterinarianRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'cedula' => ['required', 'string', 'max:20', 'regex:/^[0-9]{10}$/', 'unique:veterinarians,cedula', 'unique:users,username'],
            'first_name' => ['required', 'string', 'max:150'],
            'last_name' => ['required', 'string', 'max:150'],
            'specialty_id' => ['required', 'exists:specialties,id'],
            'email' => ['required', 'email', 'max:100', Rule::unique('users', 'email')],
            'phone' => ['nullable', 'string', 'max:20'],
            'state' => ['required', 'in:active,inactive'],
        ];
    }
}
