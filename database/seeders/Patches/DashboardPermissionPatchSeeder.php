<?php

namespace Database\Seeders\Patches;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\Role;
use App\Models\RoutePermission;

class DashboardPermissionPatchSeeder extends Seeder
{
    public function run(): void
    {
        // 1) Tạo (hoặc lấy) quyền dashboard.view
        $perm = Permission::firstOrCreate([
            'module_name' => 'dashboard',
            'action'      => 'view',
        ]);

        // 2) Gán quyền này cho role admin + staff (không tạo bản ghi trùng)
        Role::whereIn('name', ['admin', 'staff'])->get()
            ->each(fn ($role) => $role->permissions()->syncWithoutDetaching([$perm->id]));

        // 3) Đảm bảo bảng route_permissions có mapping cho dashboard ở cả admin & staff
        RoutePermission::updateOrCreate(
            ['route_name' => 'staff.dashboard'],
            ['module_name' => 'dashboard', 'action' => 'view', 'area' => 'staff', 'is_active' => true]
        );

        RoutePermission::updateOrCreate(
            ['route_name' => 'admin.dashboard'],
            ['module_name' => 'dashboard', 'action' => 'view', 'area' => 'admin', 'is_active' => true]
        );
    }
}
