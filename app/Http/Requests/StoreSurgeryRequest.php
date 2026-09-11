<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreSurgeryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $normalized = [];

        foreach (['start_time', 'end_time'] as $field) {
            $value = $this->input($field);

            if (is_string($value) && preg_match('/^\d{2}:\d{2}(?::\d{2})?$/', $value)) {
                $normalized[$field] = substr($value, 0, 5);
            }
        }

        $this->merge($normalized);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'pet_id' => ['required', 'exists:pets,id'],
            'veterinarian_id' => ['required', 'exists:veterinarians,id'],
            'operating_room_id' => ['required', 'exists:operating_rooms,id'],
            'surgery_type_id' => ['required', 'exists:surgery_types,id'],
            'scheduled_date' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
