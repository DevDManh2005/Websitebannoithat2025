<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @mixin \Illuminate\Http\Request
 */
class BlogCategoryRequest extends FormRequest
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
            'name'        => ['required','string','max:255'],
            'slug'        => ['nullable','string','max:255', Rule::unique('blog_categories','slug')->ignore($id)],
            'description' => ['nullable','string'],
            'thumbnail'   => ['nullable','image','max:2048'],
            'is_active'   => ['sometimes','boolean'],
        ];
    }
}
