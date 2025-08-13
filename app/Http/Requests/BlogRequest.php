<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @mixin \Illuminate\Http\Request
 */
class BlogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // IDE-friendly: lấy id từ hidden input trong form edit
        $id = (int) (request()->input('id') ?? 0);

        return [
            'title'        => ['required','string','max:255'],
            'slug'         => ['nullable','string','max:255', Rule::unique('blogs','slug')->ignore($id)],
            'excerpt'      => ['nullable','string'],
            'content'      => ['required','string'],
            'category_id'  => ['nullable','exists:blog_categories,id'],
            'thumbnail'    => ['nullable','image','max:4096'],
            'is_published' => ['sometimes','boolean'],
        ];
    }
}
