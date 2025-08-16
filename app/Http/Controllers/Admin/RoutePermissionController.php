<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RoutePermission;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Route as RouteFacade;
use Illuminate\Validation\Rule;

class RoutePermissionController extends Controller
{
    // =========================
    // Index
    // =========================
    public function index(Request $request)
    {
        $rows = RoutePermission::query()
            ->when($request->filled('q'), function ($q) use ($request) {
                $kw = trim($request->q);
                $q->where(function ($qq) use ($kw) {
                    $qq->where('route_name', 'like', "%{$kw}%")
                       ->orWhere('module_name', 'like', "%{$kw}%")
                       ->orWhere('action', 'like', "%{$kw}%")
                       ->orWhere('area', 'like', "%{$kw}%");
                });
            })
            ->orderBy('area')
            ->orderBy('module_name')
            ->orderBy('action')
            ->paginate(20);

        return view('admins.route_permissions.index', compact('rows'));
    }

    // =========================
    // Create
    // =========================
    public function create()
    {
        [$availablePerms, $moduleLabels, $actionLabels] = $this->buildFormData();
        $routesByArea = $this->listRoutesByArea(); // ['admin'=>[], 'staff'=>[]]

        return view('admins.route_permissions.create', compact(
            'routesByArea', 'availablePerms', 'moduleLabels', 'actionLabels'
        ));
    }

    // =========================
    // Store
    // =========================
    public function store(Request $request)
    {
        [$availablePerms] = $this->buildFormData();
        $allowedPairs = $this->flattenPairs($availablePerms); // ['orders|view','orders|update',...]

        $data = $request->validate([
            'route_name' => ['required','string','max:191','unique:route_permissions,route_name'],
            'area'       => ['nullable','in:admin,staff'],
            'pair'       => ['required', Rule::in($allowedPairs)],
            'is_active'  => ['nullable','boolean'],
        ], [
            'route_name.unique' => 'Route này đã được ánh xạ.',
            'pair.required'     => 'Vui lòng chọn chức năng.',
            'pair.in'           => 'Chức năng không hợp lệ.',
        ]);

        // Tách module_name & action từ pair
        [$module, $action] = explode('|', $data['pair'], 2);

        // Nếu không chọn area hoặc chọn sai, tự đoán theo prefix route
        $area = $this->normalizeAreaByRoute($data['route_name'], $data['area'] ?? null);

        RoutePermission::create([
            'route_name'  => $data['route_name'],
            'module_name' => $module,
            'action'      => $action,
            'area'        => $area,
            'is_active'   => (bool) ($data['is_active'] ?? 1),
        ]);

        return redirect()->route('admin.route-permissions.index')
            ->with('success', 'Đã tạo ánh xạ Route ↔ Quyền.');
    }

    // =========================
    // Edit
    // =========================
    public function edit(RoutePermission $routePermission)
    {
        [$availablePerms, $moduleLabels, $actionLabels] = $this->buildFormData();
        $routesByArea = $this->listRoutesByArea();

        return view('admins.route_permissions.edit', compact(
            'routePermission', 'routesByArea', 'availablePerms', 'moduleLabels', 'actionLabels'
        ));
    }

    // =========================
    // Update
    // =========================
    public function update(Request $request, RoutePermission $routePermission)
    {
        [$availablePerms] = $this->buildFormData();
        $allowedPairs = $this->flattenPairs($availablePerms);

        $data = $request->validate([
            'route_name' => [
                'required','string','max:191',
                Rule::unique('route_permissions', 'route_name')->ignore($routePermission->id),
            ],
            'area'       => ['nullable','in:admin,staff'],
            'pair'       => ['required', Rule::in($allowedPairs)],
            'is_active'  => ['nullable','boolean'],
        ], [
            'route_name.unique' => 'Route này đã được ánh xạ.',
            'pair.required'     => 'Vui lòng chọn chức năng.',
            'pair.in'           => 'Chức năng không hợp lệ.',
        ]);

        [$module, $action] = explode('|', $data['pair'], 2);
        $area = $this->normalizeAreaByRoute($data['route_name'], $data['area'] ?? null);

        $routePermission->update([
            'route_name'  => $data['route_name'],
            'module_name' => $module,
            'action'      => $action,
            'area'        => $area,
            'is_active'   => (bool) ($data['is_active'] ?? 0),
        ]);

        return redirect()->route('admin.route-permissions.index')
            ->with('success', 'Đã cập nhật ánh xạ.');
    }

    // =========================
    // Destroy
    // =========================
    public function destroy(RoutePermission $routePermission)
    {
        $routePermission->delete();

        return redirect()->route('admin.route-permissions.index')
            ->with('success', 'Đã xóa ánh xạ.');
    }

    // ===================================================
    // Helpers
    // ===================================================

    /**
     * Gom route name theo khu admin/staff để đổ vào select.
     * @return array{admin: string[], staff: string[]}
     */
    protected function listRoutesByArea(): array
    {
        $names = collect(RouteFacade::getRoutes())
            ->map(fn($r) => $r->getName())
            ->filter()
            ->unique()
            ->values();

        $admin = $names->filter(fn($n) => str_starts_with($n, 'admin.'))
                       ->sort()->values()->all();

        $staff = $names->filter(fn($n) => str_starts_with($n, 'staff.'))
                       ->sort()->values()->all();

        return [
            'admin' => $admin,
            'staff' => $staff,
        ];
    }

    /**
     * Chuẩn hóa area theo prefix route nếu có.
     */
    protected function normalizeAreaByRoute(string $routeName, ?string $area): string
    {
        if (str_starts_with($routeName, 'admin.')) return 'admin';
        if (str_starts_with($routeName, 'staff.')) return 'staff';
        // fallback: dùng area request hoặc mặc định staff
        return in_array($area, ['admin','staff'], true) ? $area : 'staff';
    }

    /**
     * Xây dữ liệu cho form: availablePerms, moduleLabels, actionLabels.
     * Đồng bộ với PermissionController để tên hiển thị nhất quán.
     *
     * @return array{0: array<string,string[]>, 1: array<string,string>, 2: array<string,string>}
     */
    protected function buildFormData(): array
    {
        // Các module & action bạn đang dùng
        $availablePerms = [
            'orders'          => ['view','update','ready_to_ship','cod_paid'],
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
        ];

        // Có thể lọc module theo config nếu muốn:
        // $staffMap = config('staff_modules', []);
        // $availablePerms = array_intersect_key($availablePerms, $staffMap);

        return [$availablePerms, $moduleLabels, $actionLabels];
    }

    /**
     * Tạo danh sách 'module|action' hợp lệ để Rule::in(...)
     * @param array<string,string[]> $availablePerms
     * @return string[]
     */
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
