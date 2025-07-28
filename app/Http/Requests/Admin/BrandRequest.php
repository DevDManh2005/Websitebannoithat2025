<?php
namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class BrandRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->user()->hasPermission('brand','manage');
    }

    public function rules(): array
    {
        $id = $this->route('brand')?->id;
        return [
            'name' => 'required|string|max:255|unique:brands,name,'.$id,
            'slug' => 'required|string|max:255|unique:brands,slug,'.$id,
            'logo' => ($this->isMethod('POST')?'required':'nullable').'|image|max:2048',
        ];
    }
}