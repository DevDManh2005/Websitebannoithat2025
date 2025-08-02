<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Slide;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SlideController extends Controller
{
    /**
     * Hiển thị danh sách các slide.
     */
    public function index()
    {
        $slides = Slide::orderBy('position')->get();
        return view('admins.slides.index', compact('slides'));
    }

    /**
     * Hiển thị form tạo slide mới.
     */
    public function create()
    {
        // Truyền một biến slide rỗng để form không báo lỗi
        $slide = new Slide();
        return view('admins.slides.create', compact('slide'));
    }

    /**
     * Lưu slide mới vào database.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string',
            'image' => 'required|image|max:2048', // Tối đa 2MB
            'button_text' => 'nullable|string|max:255',
            'button_link' => 'nullable|url',
            'position' => 'required|integer',
            'is_active' => 'boolean',
        ]);

        $data['is_active'] = $request->has('is_active');
        $data['image'] = $request->file('image')->store('slides', 'public');

        Slide::create($data);

        return redirect()->route('admin.slides.index')->with('success', 'Tạo slide mới thành công.');
    }

    /**
     * Hiển thị form chỉnh sửa slide.
     */
    public function edit(Slide $slide)
    {
        return view('admins.slides.edit', compact('slide'));
    }

    /**
     * Cập nhật thông tin slide.
     */
    public function update(Request $request, Slide $slide)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'button_text' => 'nullable|string|max:255',
            'button_link' => 'nullable|url',
            'position' => 'required|integer',
            'is_active' => 'boolean',
        ]);

        $data['is_active'] = $request->has('is_active');

        if ($request->hasFile('image')) {
            // Xóa ảnh cũ trước khi upload ảnh mới
            if ($slide->image) {
                Storage::disk('public')->delete($slide->image);
            }
            $data['image'] = $request->file('image')->store('slides', 'public');
        }

        $slide->update($data);

        return redirect()->route('admin.slides.index')->with('success', 'Cập nhật slide thành công.');
    }

    /**
     * Xóa slide.
     */
    public function destroy(Slide $slide)
    {
        if ($slide->image) {
            Storage::disk('public')->delete($slide->image);
        }
        $slide->delete();
        return back()->with('success', 'Đã xóa slide.');
    }
}
