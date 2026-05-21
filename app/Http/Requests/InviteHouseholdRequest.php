<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InviteHouseholdRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Only household owner can invite
        return auth()->user()->household->owner_id === auth()->id();
    }

    public function rules(): array
    {
        return [
            'email' => 'required|email|exists:users,email',
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.exists' => 'User dengan email tersebut tidak ditemukan',
        ];
    }
}
