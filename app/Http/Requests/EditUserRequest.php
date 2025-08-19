<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EditUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Gestione autorizzazioni già gestita
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($this->user->id),
            ],
            'role' => 'in:user,admin,superadmin',
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique'  => 'Questa email è già stata usata.'
        ];
    }
}
