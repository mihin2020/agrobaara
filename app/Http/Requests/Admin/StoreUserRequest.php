<?php

namespace App\Http\Requests\Admin;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isSuperAdmin();
    }

    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'min:2', 'max:100'],
            'last_name'  => ['required', 'string', 'min:2', 'max:100'],
            'email'      => ['required', 'email', 'max:150', 'unique:users,email'],
            'role'       => ['required', 'string', Rule::in(UserRole::values())],
            'password'   => [
                'required',
                'confirmed',
                Password::min(8)->mixedCase()->numbers()->symbols(),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'first_name.required' => 'Le prénom est obligatoire.',
            'last_name.required'  => 'Le nom est obligatoire.',
            'email.required'      => "L'e-mail est obligatoire.",
            'email.unique'        => 'Cet e-mail est déjà utilisé.',
            'role.required'       => 'Le rôle est obligatoire.',
            'role.in'             => 'Ce rôle est invalide.',
            'password.required'   => 'Le mot de passe est obligatoire.',
        ];
    }
}
