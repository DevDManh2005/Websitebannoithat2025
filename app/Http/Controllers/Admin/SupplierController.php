<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::orderBy('name')->paginate(10);
        return view('admins.suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        return view('admins.suppliers.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'         => 'required|string|max:255|unique:suppliers,name',
            'contact_name' => 'nullable|string|max:255',
            'phone'        => 'nullable|string|max:50',
            'email'        => 'nullable|email|max:255',
            'address'      => 'nullable|string|max:500',
            'is_active'    => 'boolean',
        ]);

        Supplier::create($data);

        return redirect()->route('admin.suppliers.index')
                         ->with('success', 'Tạo nhà cung cấp thành công');
    }

    public function show(Supplier $supplier)
    {
        return view('admins.suppliers.show', compact('supplier'));
    }

    public function edit(Supplier $supplier)
    {
        return view('admins.suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $data = $request->validate([
            'name'         => 'required|string|max:255|unique:suppliers,name,' . $supplier->id,
            'contact_name' => 'nullable|string|max:255',
            'phone'        => 'nullable|string|max:50',
            'email'        => 'nullable|email|max:255',
            'address'      => 'nullable|string|max:500',
            'is_active'    => 'boolean',
        ]);

        $supplier->update($data);

        return redirect()->route('admin.suppliers.index')
                         ->with('success', 'Cập nhật nhà cung cấp thành công');
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();
        return redirect()->route('admin.suppliers.index')
                         ->with('success', 'Xóa nhà cung cấp thành công');
    }
}
