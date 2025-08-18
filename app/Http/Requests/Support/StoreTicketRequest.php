<?php

namespace App\Http\Requests\Support;

use Illuminate\Foundation\Http\FormRequest;

class StoreTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'subject'    => ['required', 'string', 'min:3', 'max:255'],
            // cho phép tạo vé chỉ có file (nếu bạn muốn bắt buộc có message, bỏ required_without tại đây)
            'message'    => ['nullable', 'string', 'min:1', 'required_without:attachment'],
            'attachment' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,pdf,doc,docx,xls,xlsx', 'max:4096', 'required_without:message'],
        ];
    }

    public function messages(): array
    {
        return [
            'subject.required'            => 'Vui lòng nhập tiêu đề.',
            'message.required_without'    => 'Vui lòng nhập nội dung hoặc đính kèm tệp.',
            'attachment.required_without' => 'Vui lòng nhập nội dung hoặc đính kèm tệp.',
        ];
    }
}
