<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Support\RoutePermissionSync;

class PermissionController extends Controller
{
    public function index()
    {
        $perms = Permission::orderBy('module_name')->orderBy('action')->paginate(20);
        return view('admins.permissions.index', compact('perms'));
    }

    public function create()
    {
        [$availablePerms, $moduleLabels, $actionLabels] = $this->buildFormData();
        return view('admins.permissions.create', compact('availablePerms','moduleLabels','actionLabels'));
    }

    public function store(Request $request)
    {
        [$availablePerms] = $this->buildFormData();
        $allowedPairs = $this->flattenPairs($availablePerms);

        $data = $request->validate([
            'pair' => ['required', Rule::in($allowedPairs)],
        ], [
            'pair.required' => 'Vui lòng chọn chức năng.',
            'pair.in'       => 'Chức năng không hợp lệ.',
        ]);

        [$module, $action] = explode('|', $data['pair'], 2);

        // Chống trùng module + action
        $exists = Permission::where('module_name', $module)
            ->where('action', $action)
            ->exists();

        if ($exists) {
            return back()->withInput()->withErrors([
                'pair' => 'Quyền này đã tồn tại (trùng chức năng).',
            ]);
        }

        $perm = Permission::create([
            'module_name' => $module,
            'action'      => $action,
        ]);

        // Auto-sync mapping route_permissions (nếu có route khớp)
        RoutePermissionSync::syncOne('staff', $perm->module_name, $perm->action);
        RoutePermissionSync::syncOne('admin', $perm->module_name, $perm->action);

        return redirect()->route('admin.permissions.index')
            ->with('success', 'Đã tạo quyền.');
    }

    public function edit(Permission $permission)
    {
        [$availablePerms, $moduleLabels, $actionLabels] = $this->buildFormData();
        return view('admins.permissions.edit', compact('permission','availablePerms','moduleLabels','actionLabels'));
    }

    public function update(Request $request, Permission $permission)
    {
        [$availablePerms] = $this->buildFormData();
        $allowedPairs = $this->flattenPairs($availablePerms);

        $data = $request->validate([
            'pair' => ['required', Rule::in($allowedPairs)],
        ], [
            'pair.required' => 'Vui lòng chọn chức năng.',
            'pair.in'       => 'Chức năng không hợp lệ.',
        ]);

        [$module, $action] = explode('|', $data['pair'], 2);

        // Chống trùng với bản ghi khác
        $exists = Permission::where('module_name', $module)
            ->where('action', $action)
            ->where('id', '!=', $permission->id)
            ->exists();

        if ($exists) {
            return back()->withInput()->withErrors([
                'pair' => 'Quyền này đã tồn tại (trùng chức năng).',
            ]);
        }

        $permission->update([
            'module_name' => $module,
            'action'      => $action,
        ]);

        RoutePermissionSync::syncOne('staff', $permission->module_name, $permission->action);
        RoutePermissionSync::syncOne('admin', $permission->module_name, $permission->action);

        return redirect()->route('admin.permissions.index')
            ->with('success', 'Đã cập nhật quyền.');
    }

    public function destroy(Permission $permission)
    {
        $permission->delete();
        return back()->with('success', 'Đã xoá quyền.');
    }

    // ================= Helpers =================

    
protected function buildFormData(): array
{
    // Những cặp module ⇒ action cho phép tạo
    $availablePerms = [
        'orders'          => ['view','create','update','delete','ready_to_ship','cod_paid'],
        'reviews'         => ['view','moderate','delete'],
        'products'        => ['view','create','update','delete'],
        'categories'      => ['view','create','update','delete'],
        'brands'          => ['view','create','update','delete'],
        'suppliers'       => ['view','create','update','delete'],
        'inventories'     => ['view','create','update','delete'],
        'vouchers'        => ['view','create','update','delete'],
        'slides'          => ['view','create','update','delete'],
        'blogs'           => ['view','create','update','delete'],
        'blog-categories' => ['view','create','update','delete'],
        'uploads'         => ['update'],

        // ➜ THÊM NHÓM HỖ TRỢ
        'support-tickets' => ['view','update','reply','delete'],
    ];

    // Nhãn tiếng Việt cho module
    $moduleLabels = [
        'orders'          => 'Đơn hàng',
        'reviews'         => 'Đánh giá',
        'products'        => 'Sản phẩm',
        'categories'      => 'Danh mục',
        'brands'          => 'Thương hiệu',
        'suppliers'       => 'Nhà cung cấp',
        'inventories'     => 'Kho hàng',
        'vouchers'        => 'Voucher',
        'slides'          => 'Slide',
        'blogs'           => 'Bài viết',
        'blog-categories' => 'Chuyên mục',
        'uploads'         => 'Tải lên',

        // ➜ NHÃN HỖ TRỢ
        'support-tickets' => 'Hỗ trợ',
    ];

    // Nhãn tiếng Việt cho action
    $actionLabels = [
        'view'          => 'Xem',
        'create'        => 'Thêm',
        'update'        => 'Cập nhật',
        'delete'        => 'Xóa',
        'moderate'      => 'Duyệt/Ẩn',
        'ready_to_ship' => 'Sẵn sàng giao',
        'cod_paid'      => 'Đã thu COD',

        // ➜ NHÃN CHO ACTION MỚI
        'reply'         => 'Gửi phản hồi',
    ];

    return [$availablePerms, $moduleLabels, $actionLabels];
}
    protected function flattenPairs(array $availablePerms): array
    {
        $pairs = [];
        foreach ($availablePerms as $m => $actions) {
            foreach ($actions as $a) {
                $pairs[] = "{$m}|{$a}";
            }
        }
        return $pairs;
    }
}
