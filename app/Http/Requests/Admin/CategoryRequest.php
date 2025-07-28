<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->user()->hasPermission('category','manage');
    }

    public function rules(): array
    {
        $id = $this->route('category')?->id;
        return [
            'name'      => 'required|string|max:255|unique:categories,name,'.$id,
            'slug'      => 'required|string|max:255|unique:categories,slug,'.$id,
            'parent_id' => 'nullable|exists:categories,id',
            'is_active' => 'boolean',
            'position'  => 'nullable|integer',
        ];
    }
}