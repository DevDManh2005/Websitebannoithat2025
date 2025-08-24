<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'receiver_name' => ['required', 'string', 'max:255'],
            'phone'         => ['required', 'regex:/^(0[1-9][0-9]{8,9}|(\+84)[1-9][0-9]{7,9})$/'],
            'city'          => ['required', 'string', 'max:255'],
            'district'      => ['required', 'string', 'max:255'],
            'ward'          => ['required', 'string', 'max:255'],
            'address'       => ['required', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'phone.required' => 'Vui lòng nhập số điện thoại.',
            'phone.regex'    => 'Số điện thoại không hợp lệ. Vui lòng nhập đúng định dạng.',
        ];
    }
}
