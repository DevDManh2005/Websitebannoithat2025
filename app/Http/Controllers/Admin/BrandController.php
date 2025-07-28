<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BrandController extends Controller
{
    public function index()
    {
        $brands = Brand::orderBy('name')->paginate(10);
        return view('admins.brands.index', compact('brands'));
    }

    public function create()
    {
        return view('admins.brands.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:255|unique:brands,name',
            'logo_file' => 'nullable|image|max:2048',
            'logo_url'  => 'nullable|url|max:255',
            'is_active' => 'required|boolean',
        ]);

        // Xác định logo: ưu tiên file upload, nếu không thì lấy URL
        $logo = null;
        if ($request->hasFile('logo_file')) {
            $logo = $request->file('logo_file')->store('brands', 'public');
        } elseif (! empty($validated['logo_url'])) {
            $logo = $validated['logo_url'];
        }

        Brand::create([
            'name'      => $validated['name'],
            'logo'      => $logo,
            'is_active' => $validated['is_active'],
        ]);

        return redirect()
            ->route('admin.brands.index')
            ->with('success', 'Tạo thương hiệu thành công');
    }

    public function show(Brand $brand)
    {
        return view('admins.brands.show', compact('brand'));
    }

    public function edit(Brand $brand)
    {
        return view('admins.brands.edit', compact('brand'));
    }

    public function update(Request $request, Brand $brand)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:255|unique:brands,name,' . $brand->id,
            'logo_file' => 'nullable|image|max:2048',
            'logo_url'  => 'nullable|url|max:255',
            'is_active' => 'required|boolean',
        ]);

        // Giữ lại giá trị logo cũ
        $logo = $brand->logo;

        if ($request->hasFile('logo_file')) {
            // Xóa file cũ nếu là file local
            if ($logo && ! Str::startsWith($logo, ['http://', 'https://'])) {
                Storage::disk('public')->delete($logo);
            }
            $logo = $request->file('logo_file')->store('brands', 'public');

        } elseif (! empty($validated['logo_url'])) {
            // Nếu người dùng đổi sang URL
            if ($logo && ! Str::startsWith($logo, ['http://', 'https://'])) {
                Storage::disk('public')->delete($logo);
            }
            $logo = $validated['logo_url'];
        }

        $brand->update([
            'name'      => $validated['name'],
            'logo'      => $logo,
            'is_active' => $validated['is_active'],
        ]);

        return redirect()
            ->route('admin.brands.index')
            ->with('success', 'Cập nhật thương hiệu thành công');
    }

    public function destroy(Brand $brand)
    {
        // Xóa logo cũ nếu là file local
        if ($brand->logo && ! Str::startsWith($brand->logo, ['http://', 'https://'])) {
            Storage::disk('public')->delete($brand->logo);
        }

        $brand->delete();

        return redirect()
            ->route('admin.brands.index')
            ->with('success', 'Xóa thương hiệu thành công');
    }
}
