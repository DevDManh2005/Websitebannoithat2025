<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Slide;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SlideController extends Controller
{
    public function index(Request $request)
    {
        $slides = Slide::query()
            ->when($request->filled('q'), fn($q) =>
                $q->where('title', 'like', '%' . trim($request->q) . '%'))
            ->when($request->has('status') && $request->status !== '',
                fn($q) => $q->where('is_active', (int)$request->status))
            ->orderBy('position')
            ->orderByDesc('id')
            ->paginate(12);

        return view('admins.slides.index', compact('slides'));
    }

    public function create()
    {
        $slide = new Slide();
        return view('admins.slides.create', compact('slide'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'        => ['required','string','max:255'],
            'subtitle'     => ['nullable','string'],
            'image'        => ['required','image','max:2048'], // 2MB
            'button_text'  => ['nullable','string','max:255'],
            'button_link'  => ['nullable','url'],
            'position'     => ['required','integer','min:0'],
            'is_active'    => ['nullable','boolean'],
        ]);

        $data['is_active'] = (bool) ($request->is_active ?? false);
        $data['image'] = $request->file('image')->store('slides', 'public');

        Slide::create($data);

        return redirect()->route('admin.slides.index')->with('success', 'Tạo slide mới thành công.');
    }

    public function edit(Slide $slide)
    {
        return view('admins.slides.edit', compact('slide'));
    }

    public function update(Request $request, Slide $slide)
    {
        $data = $request->validate([
            'title'        => ['required','string','max:255'],
            'subtitle'     => ['nullable','string'],
            'image'        => ['nullable','image','max:2048'],
            'button_text'  => ['nullable','string','max:255'],
            'button_link'  => ['nullable','url'],
            'position'     => ['required','integer','min:0'],
            'is_active'    => ['nullable','boolean'],
            'remove_image' => ['nullable','boolean'],
        ]);

        $data['is_active'] = (bool) ($request->is_active ?? false);

        // Xoá ảnh cũ nếu người dùng bấm “Xoá ảnh”
        if ($request->boolean('remove_image') && $slide->image) {
            Storage::disk('public')->delete($slide->image);
            $data['image'] = null;
        }

        // Upload ảnh mới
        if ($request->hasFile('image')) {
            if ($slide->image) {
                Storage::disk('public')->delete($slide->image);
            }
            $data['image'] = $request->file('image')->store('slides', 'public');
        }

        $slide->update($data);

        return redirect()->route('admin.slides.index')->with('success', 'Cập nhật slide thành công.');
    }

    public function destroy(Slide $slide)
    {
        if ($slide->image) {
            Storage::disk('public')->delete($slide->image);
        }
        $slide->delete();
        return back()->with('success', 'Đã xóa slide.');
    }

    /**
     * Toggle trạng thái kích hoạt (AJAX hoặc điều hướng thường).
     */
    public function toggle(Request $request, Slide $slide)
    {
        $slide->is_active = !$slide->is_active;
        $slide->save();

        if ($request->wantsJson()) {
            return response()->json([
                'ok' => true,
                'is_active' => (bool) $slide->is_active,
                'message' => $slide->is_active ? 'Đã bật slide.' : 'Đã tắt slide.',
            ]);
        }
        return back()->with('success', $slide->is_active ? 'Đã bật slide.' : 'Đã tắt slide.');
    }
}
