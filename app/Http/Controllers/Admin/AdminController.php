<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function index()
    {
        $admins = User::where('role_id', 1)->paginate(10);
        return view('admins.admins.index', compact('admins'));
    }

    public function create()
    {
        return view('admins.admins.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|unique:users',
            'password'      => 'required|min:6|confirmed',
        ]);

        User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'role_id'  => 1,
        ]);

        return redirect()->route('admin.admins.index')
                         ->with('success', 'Tạo tài khoản Admin thành công.');
    }

    public function edit($id)
    {
        $admin = User::where('role_id', 1)->findOrFail($id);
        return view('admins.admins.edit', compact('admin'));
    }

    public function update(Request $request, $id)
    {
        $admin = User::where('role_id', 1)->findOrFail($id);

        $data = $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
        ]);

        $admin->update($data);

        return redirect()->route('admin.admins.index')
                         ->with('success', 'Cập nhật tài khoản Admin thành công.');
    }

    public function destroy($id)
    {
        User::where('role_id', 1)->findOrFail($id)->delete();

        return redirect()->route('admin.admins.index')
                         ->with('success', 'Xóa Admin thành công.');
    }
}
