<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StorePetRequest extends FormRequest
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
            'owner_id' => ['required', 'exists:owners,id'],
            'species_id' => ['required', 'exists:species,id'],
            'name' => ['required', 'string', 'max:100'],
            'breed' => ['nullable', 'string', 'max:100'],
            'age' => ['nullable', 'integer', 'min:0'],
            'weight' => ['nullable', 'numeric', 'min:0', 'max:999.99'],
            'gender' => ['required', 'in:male,female'],
            'state' => ['required', 'in:active,inactive'],
        ];
    }
}
