<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class StoreStaffRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', Password::defaults()],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:10'],
            'country' => ['nullable', 'string', 'max:100'],
            'position' => ['nullable', 'string', 'max:100'],
            'hire_date' => ['nullable', 'date'],
            'daycare_ids' => ['nullable', 'array'],
            'daycare_ids.*' => ['exists:daycares,id'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'name' => 'nom',
            'email' => 'email',
            'password' => 'mot de passe',
            'phone' => 'téléphone',
            'address' => 'adresse',
            'city' => 'ville',
            'postal_code' => 'code postal',
            'country' => 'pays',
            'position' => 'poste',
            'hire_date' => 'date d\'embauche',
            'daycare_ids' => 'crèches',
        ];
    }
}