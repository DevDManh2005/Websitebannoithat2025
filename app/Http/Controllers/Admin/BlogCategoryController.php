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
    /** Danh sách danh mục + filter đơn giản */
    public function index(Request $request): View
    {
        $q      = $request->input('q');
        $status = $request->input('status'); // '0' | '1'

        $query = BlogCategory::query()
            ->with([
                'parent:id,name',
                'children' => fn($c) => $c
                    ->orderBy('sort_order')
                    ->orderBy('name')
                    ->select(['id','name','slug','is_active','parent_id','sort_order']),
            ])
            ->orderBy('parent_id')
            ->orderBy('sort_order')
            ->orderByDesc('created_at');

        if ($q) {
            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('slug', 'like', "%{$q}%");
            });
        }

        if ($status !== null && in_array($status, ['0','1'], true)) {
            $query->where('is_active', (int) $status);
        }

        $categories = $query->paginate(15); // không dùng withQueryString để khỏi bị lsp cảnh báo

        return view('admins.blog_categories.index', compact('categories'));
    }

    /** Form tạo */
    public function create(): View
    {
        $parents = BlogCategory::orderBy('name')->get(['id', 'name']);
        return view('admins.blog_categories.create', compact('parents'));
    }

    /** Lưu mới */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'slug'        => 'nullable|string|max:255|unique:blog_categories,slug',
            'description' => 'nullable|string',
            'thumbnail'   => 'nullable|image|max:2048',
            'is_active'   => 'sometimes|boolean',
            'parent_id'   => 'nullable|integer|exists:blog_categories,id',
            'sort_order'  => 'nullable|integer|min:0',
        ]);

        $data['slug']      = $data['slug'] ?: Str::slug($data['name']);
        $data['is_active'] = $request->boolean('is_active');

        if ($file = $request->file('thumbnail')) {
            // dùng cố định disk 'public' (đã có trong config/filesystems.php)
            $data['thumbnail'] = $file->store('blog_categories', 'public');
        }

        BlogCategory::create($data);

        return redirect()->route('admin.blog-categories.index')
            ->with('success', 'Đã tạo danh mục bài viết.');
    }

    /** Form sửa */
    public function edit(BlogCategory $blog_category): View
    {
        $parents = BlogCategory::where('id', '<>', $blog_category->id)
            ->orderBy('name')->get(['id', 'name']);

        return view('admins.blog_categories.edit', [
            'category' => $blog_category,
            'parents'  => $parents,
        ]);
    }

    /** Cập nhật */
    public function update(Request $request, BlogCategory $blog_category): RedirectResponse
    {
        $data = $request->validate([
            'id'          => 'required|integer',
            'name'        => 'required|string|max:255',
            'slug'        => 'nullable|string|max:255|unique:blog_categories,slug,' . $blog_category->id,
            'description' => 'nullable|string',
            'thumbnail'   => 'nullable|image|max:2048',
            'is_active'   => 'sometimes|boolean',
            'parent_id'   => ['nullable','integer','exists:blog_categories,id','different:id'],
            'sort_order'  => 'nullable|integer|min:0',
        ]);

        $data['slug']      = $data['slug'] ?: Str::slug($data['name']);
        $data['is_active'] = $request->boolean('is_active');

        if ($file = $request->file('thumbnail')) {
            if ($blog_category->thumbnail && !Str::startsWith($blog_category->thumbnail, ['http://','https://','//'])) {
                Storage::disk('public')->delete($blog_category->thumbnail);
            }
            $data['thumbnail'] = $file->store('blog_categories', 'public');
        } else {
            unset($data['thumbnail']);
        }

        $blog_category->update($data);

        return back()->with('success', 'Đã cập nhật danh mục.');
    }

    /** Xoá */
    public function destroy(BlogCategory $blog_category): RedirectResponse
    {
        // chuyển con lên root trước khi xoá
        $blog_category->children()->update(['parent_id' => null]);

        if ($blog_category->thumbnail && !Str::startsWith($blog_category->thumbnail, ['http://','https://','//'])) {
            Storage::disk('public')->delete($blog_category->thumbnail);
        }

        $blog_category->delete();

        return back()->with('success', 'Đã xóa danh mục.');
    }
}
