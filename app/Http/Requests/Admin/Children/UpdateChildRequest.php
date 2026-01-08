<?php

namespace App\Http\Requests\Admin\Children;

use Illuminate\Foundation\Http\FormRequest;

class UpdateChildRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization handled by controller
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'daycare_id' => ['required', 'exists:daycares,id'],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'birth_date' => ['required', 'date', 'before_or_equal:today'],
            'gender' => ['nullable', 'in:male,female,other'],
            'emergency_contact' => ['nullable', 'string'],
            'enrollment_date' => ['nullable', 'date'],
            'parent_ids' => ['nullable', 'array'],
            'parent_ids.*' => ['exists:users,id'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'daycare_id' => 'crèche',
            'first_name' => 'prénom',
            'last_name' => 'nom',
            'birth_date' => 'date de naissance',
            'gender' => 'genre',
            'emergency_contact' => 'contact d\'urgence',
            'enrollment_date' => 'date d\'inscription',
            'parent_ids' => 'parents',
        ];
    }
}