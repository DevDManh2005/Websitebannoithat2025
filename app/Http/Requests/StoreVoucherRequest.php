<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreVoucherRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Hoặc thêm logic kiểm tra quyền của admin
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'code' => [
                'required',
                'string',
                'max:255',
                // Rule unique: đảm bảo mã không trùng, trừ khi đang cập nhật chính nó
                Rule::unique('vouchers')->ignore($this->voucher),
            ],
            'type' => 'required|in:fixed,percent',
            'value' => [
                'required',
                'numeric',
                'min:0',
                // Nếu là %, giá trị không được > 50
                Rule::when($this->input('type') === 'percent', ['max:50']),
            ],
            'min_order_amount' => [
                'nullable',
                'numeric',
                'min:0',
                // MỚI: Rule quan trọng nhất
                // Áp dụng khi: type=fixed VÀ min_order_amount có giá trị (>0)
                Rule::when(
                    $this->input('type') === 'fixed' && $this->input('min_order_amount') > 0,
                    ['gt:value'] // Phải lớn hơn giá trị của trường 'value'
                ),
            ],
            'usage_limit' => 'nullable|integer|min:1',
            'start_at' => 'nullable|date',
            'end_at' => 'nullable|date|after_or_equal:start_at',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Tạo thông báo lỗi tùy chỉnh (tùy chọn nhưng nên có)
     */
    public function messages(): array
    {
        return [
            'min_order_amount.gt' => 'Với giảm giá cố định, giá trị đơn hàng tối thiểu phải lớn hơn mức giảm giá.',
            'end_at.after_or_equal' => 'Ngày kết thúc phải sau hoặc bằng ngày bắt đầu.',
            // ... các message khác
        ];
    }
}