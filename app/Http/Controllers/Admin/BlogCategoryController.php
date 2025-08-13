<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BlogCategoryController extends Controller
{
    public function index(): View
    {
        $categories = BlogCategory::latest()->paginate(15);
        return view('admins.blog_categories.index', compact('categories'));
    }

    public function create(): View
    {
        return view('admins.blog_categories.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'slug'        => 'nullable|string|max:255|unique:blog_categories,slug',
            'description' => 'nullable|string',
            'thumbnail'   => 'nullable|image|max:2048',
            'is_active'   => 'sometimes|boolean',
        ]);

        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);

        /** @var \Illuminate\Http\UploadedFile|null $file */
        $file = $request->file('thumbnail');
        if ($file) {
            $data['thumbnail'] = $file->store('blog_categories', 'public');
        }

        BlogCategory::create($data);

        return redirect()->route('admin.blog-categories.index')
            ->with('success', 'Đã tạo danh mục bài viết.');
    }

    public function edit(BlogCategory $blog_category): View
    {
        return view('admins.blog_categories.edit', ['category' => $blog_category]);
    }

    public function update(Request $request, BlogCategory $blog_category): RedirectResponse
    {
        $data = $request->validate([
            'id'          => 'required|integer', // hidden input từ form edit
            'name'        => 'required|string|max:255',
            'slug'        => 'nullable|string|max:255|unique:blog_categories,slug,' . $blog_category->id,
            'description' => 'nullable|string',
            'thumbnail'   => 'nullable|image|max:2048',
            'is_active'   => 'sometimes|boolean',
        ]);

        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);

        /** @var \Illuminate\Http\UploadedFile|null $file */
        $file = $request->file('thumbnail');
        if ($file) {
            // xóa ảnh cũ nếu có
            if ($blog_category->thumbnail) {
                Storage::disk('public')->delete($blog_category->thumbnail);
            }
            $data['thumbnail'] = $file->store('blog_categories', 'public');
        } else {
            unset($data['thumbnail']);
        }

        $blog_category->update($data);

        return back()->with('success', 'Đã cập nhật danh mục.');
    }

    public function destroy(BlogCategory $blog_category): RedirectResponse
    {
        if ($blog_category->thumbnail) {
            Storage::disk('public')->delete($blog_category->thumbnail);
        }
        $blog_category->delete();

        return back()->with('success', 'Đã xóa danh mục.');
    }
}
