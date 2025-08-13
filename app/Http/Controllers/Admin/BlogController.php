<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\BlogCategory;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BlogController extends Controller
{
    public function index(): View
    {
        $blogs = Blog::with('category','author')->latest()->paginate(15);
        return view('admins.blogs.index', compact('blogs'));
    }

    public function create(): View
    {
        $categories = BlogCategory::orderBy('name')->get();
        return view('admins.blogs.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title'        => 'required|string|max:255',
            'slug'         => 'nullable|string|max:255|unique:blogs,slug',
            'excerpt'      => 'nullable|string',
            'content'      => 'required|string',
            'category_id'  => 'nullable|exists:blog_categories,id',
            'thumbnail'    => 'nullable|image|max:4096',
            'is_published' => 'sometimes|boolean',
        ]);

        $data['slug']   = $data['slug'] ?: Str::slug($data['title']);
        $data['user_id'] = auth()->id();
        $data['is_published'] = (bool) ($data['is_published'] ?? false);

        /** @var \Illuminate\Http\UploadedFile|null $file */
        $file = $request->file('thumbnail');
        if ($file) {
            $data['thumbnail'] = $file->store('blogs', 'public');
        }

        Blog::create($data);

        return redirect()->route('admin.blogs.index')->with('success', 'Đã tạo bài viết.');
    }

    public function edit(Blog $blog): View
    {
        $categories = BlogCategory::orderBy('name')->get();
        return view('admins.blogs.edit', compact('blog','categories'));
    }

    public function update(Request $request, Blog $blog): RedirectResponse
    {
        $data = $request->validate([
            'id'           => 'required|integer', // hidden input từ form edit
            'title'        => 'required|string|max:255',
            'slug'         => 'nullable|string|max:255|unique:blogs,slug,' . $blog->id,
            'excerpt'      => 'nullable|string',
            'content'      => 'required|string',
            'category_id'  => 'nullable|exists:blog_categories,id',
            'thumbnail'    => 'nullable|image|max:4096',
            'is_published' => 'sometimes|boolean',
        ]);

        $data['slug'] = $data['slug'] ?: Str::slug($data['title']);
        $data['is_published'] = (bool) ($data['is_published'] ?? false);

        /** @var \Illuminate\Http\UploadedFile|null $file */
        $file = $request->file('thumbnail');
        if ($file) {
            if ($blog->thumbnail) {
                Storage::disk('public')->delete($blog->thumbnail);
            }
            $data['thumbnail'] = $file->store('blogs', 'public');
        } else {
            unset($data['thumbnail']);
        }

        $blog->update($data);

        return back()->with('success', 'Đã cập nhật bài viết.');
    }

    public function destroy(Blog $blog): RedirectResponse
    {
        if ($blog->thumbnail) {
            Storage::disk('public')->delete($blog->thumbnail);
        }
        $blog->delete();

        return back()->with('success', 'Đã xóa bài viết.');
    }
}
