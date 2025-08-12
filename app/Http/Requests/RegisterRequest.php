<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'     => 'required|string|max:255|not_regex:/[0-9]/',
            'email'    => 'required|email|unique:users|regex:/@gmail\.com$/i',
            'password' => 'required|string|min:9|confirmed',
        ];
    }
}
