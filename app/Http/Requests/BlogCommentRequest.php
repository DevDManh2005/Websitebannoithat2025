<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BlogCommentRequest extends FormRequest
{
    public function authorize(): bool { return auth()->check(); }
    public function rules(): array {
        return [
            'comment' => 'required|string|max:3000',
            'parent_id' => 'nullable|exists:blog_comments,id'
        ];
    }
}
