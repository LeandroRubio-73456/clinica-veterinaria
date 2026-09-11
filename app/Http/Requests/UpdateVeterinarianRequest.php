<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateVeterinarianRequest extends FormRequest
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
            'cedula' => ['required', 'string', 'max:20', 'regex:/^[0-9]{10}$/', Rule::unique('veterinarians', 'cedula')->ignore($this->route('veterinarian')), Rule::unique('users', 'username')->ignore($this->route('veterinarian')?->user_id)],
            'first_name' => ['required', 'string', 'max:150'],
            'last_name' => ['required', 'string', 'max:150'],
            'specialty_id' => ['required', 'exists:specialties,id'],
            'email' => ['required', 'email', 'max:100', Rule::unique('veterinarians', 'email')->ignore($this->route('veterinarian')), Rule::unique('users', 'email')->ignore($this->route('veterinarian')?->user_id)],
            'phone' => ['nullable', 'string', 'max:20'],
        ];
    }
}
