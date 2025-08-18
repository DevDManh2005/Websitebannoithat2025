<?php

namespace App\Http\Requests\Support;

use Illuminate\Foundation\Http\FormRequest;

class StoreReplyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            // Không đặt min:1 vì có thể chỉ gửi file
            'message'    => 'nullable|string',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf,doc,docx,xls,xlsx|max:4096',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($v) {
            /** @var \Illuminate\Http\Request $req */
            $req = request();

            $message = trim((string) $req->input('message', ''));
            $hasFile = $req->hasFile('attachment');

            if ($message === '' && !$hasFile) {
                $v->errors()->add('message', 'Bạn cần nhập nội dung hoặc đính kèm tệp.');
            }
        });
    }
}
