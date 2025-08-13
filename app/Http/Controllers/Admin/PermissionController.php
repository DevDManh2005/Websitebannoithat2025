<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Http\Request;
use App\Support\RoutePermissionSync; // <— THÊM

class PermissionController extends Controller
{
    public function index()
    {
        $perms = Permission::orderBy('module_name')->orderBy('action')->paginate(20);
        return view('admins.permissions.index', compact('perms'));
    }

    public function create()
    {
        return view('admins.permissions.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'module_name' => 'required|string|max:64',
            'action'      => 'required|string|max:16|in:view,create,update,delete',
        ]);

        $perm = Permission::create($data);

        // AUTO-SYNC cho cả admin & staff (nếu có route tương ứng)
        RoutePermissionSync::syncOne('staff', $perm->module_name, $perm->action);
        RoutePermissionSync::syncOne('admin', $perm->module_name, $perm->action);

        return redirect()->route('admin.permissions.index')->with('success','Đã tạo quyền.');
    }

    public function edit(Permission $permission)
    {
        return view('admins.permissions.edit', compact('permission'));
    }

    public function update(Request $request, Permission $permission)
    {
        $data = $request->validate([
            'module_name' => 'required|string|max:64',
            'action'      => 'required|string|max:16|in:view,create,update,delete',
        ]);

        $permission->update($data);

        // AUTO-SYNC cập nhật mapping
        RoutePermissionSync::syncOne('staff', $permission->module_name, $permission->action);
        RoutePermissionSync::syncOne('admin', $permission->module_name, $permission->action);

        return redirect()->route('admin.permissions.index')->with('success','Đã cập nhật quyền.');
    }

    public function destroy(Permission $permission)
    {
        $permission->delete();
        return back()->with('success','Đã xoá quyền.');
    }
}
