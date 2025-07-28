<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::with('parent')
                              ->orderBy('position')
                              ->paginate(10);

        return view('admins.categories.index', compact('categories'));
    }

    public function create()
    {
        $parents = Category::whereNull('parent_id')->get();
        return view('admins.categories.create', compact('parents'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'      => 'required|string|max:255|unique:categories,name',
            'parent_id' => 'nullable|exists:categories,id',
            'is_active' => 'nullable|boolean',
            'position'  => 'nullable|integer|min:0',
        ]);

        // Tạo slug từ name
        $data['slug'] = Str::slug($data['name']);

        // Nếu checkbox is_active không check thì mặc định false
        $data['is_active'] = $request->has('is_active');

        // Nếu không nhập position thì mặc định là 0
        $data['position'] = $data['position'] ?? 0;

        Category::create($data);

        return redirect()->route('admin.categories.index')
                         ->with('success', 'Tạo danh mục thành công');
    }

    public function show(Category $category)
    {
        return view('admins.categories.show', compact('category'));
    }

    public function edit(Category $category)
    {
        $parents = Category::whereNull('parent_id')
                           ->where('id', '!=', $category->id)
                           ->get();

        return view('admins.categories.edit', compact('category', 'parents'));
    }

    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'name'      => 'required|string|max:255|unique:categories,name,' . $category->id,
            'parent_id' => 'nullable|exists:categories,id',
            'is_active' => 'nullable|boolean',
            'position'  => 'nullable|integer|min:0',
        ]);

        // Tạo slug từ name
        $data['slug'] = Str::slug($data['name']);

        // Xử lý is_active và position giống store()
        $data['is_active'] = $request->has('is_active');
        $data['position']  = $data['position'] ?? 0;

        $category->update($data);

        return redirect()->route('admin.categories.index')
                         ->with('success', 'Cập nhật danh mục thành công');
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return redirect()->route('admin.categories.index')
                         ->with('success', 'Xóa danh mục thành công');
    }
}
