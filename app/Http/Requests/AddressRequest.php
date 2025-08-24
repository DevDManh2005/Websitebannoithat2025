<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddressRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Cho phép mọi user đã login dùng
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'receiver_name' => ['required', 'string', 'max:255'],
            'phone' => [
                'required',
                'string',
                'max:20',
                'regex:/^(0[1-9][0-9]{8,9}|(\+84)[1-9][0-9]{7,9})$/'
            ],

            'city'          => ['required', 'string', 'max:255'],
            'district'      => ['required', 'string', 'max:255'],
            'ward'          => ['required', 'string', 'max:255'],
            'address'       => ['required', 'string', 'max:255'],

            // các trường chỉ dùng khi checkout
            'note'          => ['nullable', 'string'],
            'shipping_fee'  => ['nullable', 'numeric', 'min:0'],
            'payment_method' => ['nullable', 'in:cod,vnpay'],

            // ID hành chính (dùng nếu bạn cần mapping API GHN/GHTK)
            'province_id'   => ['nullable', 'integer'],
            'district_id'   => ['nullable', 'integer'],
            'ward_code'     => ['nullable', 'string', 'max:50'],
        ];
    }

    public function messages(): array
    {
        return [
            'receiver_name.required' => 'Vui lòng nhập tên người nhận.',
            'phone.required'         => 'Vui lòng nhập số điện thoại.',
            'city.required'          => 'Vui lòng chọn Tỉnh/Thành phố.',
            'district.required'      => 'Vui lòng chọn Quận/Huyện.',
            'ward.required'          => 'Vui lòng chọn Phường/Xã.',
            'address.required'       => 'Vui lòng nhập địa chỉ cụ thể.',

            'shipping_fee.numeric'   => 'Phí vận chuyển không hợp lệ.',
            'payment_method.in'      => 'Phương thức thanh toán không hợp lệ.',
        ];
    }
}
