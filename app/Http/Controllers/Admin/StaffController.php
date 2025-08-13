<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Permission;
use Illuminate\Support\Facades\Hash;
use App\Support\RoutePermissionSync;

class StaffController extends Controller
{
    public function index()
    {
        $staffs = User::where('role_id', 2)
            ->with(['directPermissions', 'role']) // thêm cái này
            ->paginate(10);

        return view('admins.staffs.index', compact('staffs'));
    }


    public function create()
    {
        // Lấy danh sách permission nhóm theo module
        $modules = Permission::all()->groupBy('module_name');
        return view('admins.staffs.create', compact('modules'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'                  => 'required|string|max:255',
            'email'                 => 'required|email|unique:users',
            'password'              => 'required|min:6|confirmed',
            'password_confirmation' => 'required',
            'permissions'           => 'array',
            'permissions.*'         => 'exists:permissions,id',
        ]);

        // Tạo user với role staff
        $staff = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'role_id'  => 2,
        ]);

        // Gán permissions
        $staff->directPermissions()->sync($data['permissions'] ?? []);
        // AUTO-SYNC: đồng bộ mapping cho các quyền vừa gán (khu staff)
        $perms = Permission::whereIn('id', $data['permissions'] ?? [])->get();
        RoutePermissionSync::syncMany('staff', $perms);
        return redirect()->route('admin.staffs.index')
            ->with('success', 'Tạo tài khoản nhân viên thành công.');
    }

    public function edit($id)
    {
        $staff = User::where('role_id', 2)
            ->with(['permissions', 'role.permissions'])
            ->findOrFail($id);

        $modules  = Permission::all()->groupBy('module_name');
        $assigned = $staff->directPermissions->pluck('id')->all();

        return view('admins.staffs.edit', compact('staff', 'modules', 'assigned'));
    }

    public function update(Request $request, $id)
    {
        $staff = User::where('role_id', 2)->findOrFail($id);

        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => 'required|email|unique:users,email,' . $id,
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        // Cập nhật thông tin cơ bản
        $staff->update([
            'name'  => $data['name'],
            'email' => $data['email'],
        ]);

        // Cập nhật permissions
        $staff->directPermissions()->sync($data['permissions'] ?? []);
        // AUTO-SYNC khu staff
        $perms = Permission::whereIn('id', $data['permissions'] ?? [])->get();
        RoutePermissionSync::syncMany('staff', $perms);
        return redirect()->route('admin.staffs.index')
            ->with('success', 'Cập nhật thông tin nhân viên thành công.');
    }

    public function destroy($id)
    {
        $staff = User::where('role_id', 2)->findOrFail($id);
        $staff->delete();

        return redirect()->route('admin.staffs.index')
            ->with('success', 'Xóa nhân viên thành công.');
    }
}
