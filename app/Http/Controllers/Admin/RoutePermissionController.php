<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RoutePermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rule;

class RoutePermissionController extends Controller
{
    /** Danh sách route có tên để chọn (lọc admin.* và staff.*) */
    protected function namedRoutes(): array
    {
        $routes = [];
        foreach (Route::getRoutes() as $r) {
            $name = $r->getName();
            if (!$name) continue;

            // chỉ quan tâm admin.* và staff.* và bỏ chính nó
            if (
                (str_starts_with($name, 'admin.') || str_starts_with($name, 'staff.')) &&
                !str_starts_with($name, 'admin.route-permissions.')
            ) {
                $routes[$name] = sprintf('%s  ⟶  %s@%s',
                    $name,
                    $r->getAction()['controller'] ?? 'Closure',
                    $r->getActionMethod() ?? '-'
                );
            }
        }
        ksort($routes);
        return $routes;
    }

    public function index(Request $request)
    {
        $q = trim($request->get('q', ''));

        $perms = RoutePermission::query()
            ->when($q, function ($qr) use ($q) {
                $qr->where(function ($x) use ($q) {
                    $x->where('route_name', 'like', "%$q%")
                      ->orWhere('module_name', 'like', "%$q%")
                      ->orWhere('action', 'like', "%$q%");
                });
            })
            ->orderBy('route_name')
            ->paginate(20);

        return view('admins.route_permissions.index', [
            'perms'  => $perms,
            'q'      => $q,
        ]);
    }

    public function create()
    {
        return view('admins.route_permissions.create', [
            'routes' => $this->namedRoutes(),
            'rp'     => new RoutePermission(),
        ]);
    }

    public function store(Request $request)
    {
        $routes = $this->namedRoutes();

        $data = $request->validate([
            'route_name'  => ['required','string','unique:route_permissions,route_name', Rule::in(array_keys($routes))],
            'module_name' => ['required','string','max:255'],
            'action'      => ['required','string','max:255'],
        ]);

        RoutePermission::create($data);

        return redirect()->route('admin.route-permissions.index')
            ->with('success', 'Đã thêm mapping route → quyền.');
    }

    public function edit(RoutePermission $route_permission)
    {
        return view('admins.route_permissions.edit', [
            'routes' => $this->namedRoutes(),
            'rp'     => $route_permission,
        ]);
    }

    public function update(Request $request, RoutePermission $route_permission)
    {
        $routes = $this->namedRoutes();

        $data = $request->validate([
            'route_name'  => ['required','string',Rule::unique('route_permissions','route_name')->ignore($route_permission->id), Rule::in(array_keys($routes))],
            'module_name' => ['required','string','max:255'],
            'action'      => ['required','string','max:255'],
        ]);

        $route_permission->update($data);

        return redirect()->route('admin.route-permissions.index')
            ->with('success', 'Đã cập nhật mapping.');
    }

    public function destroy(RoutePermission $route_permission)
    {
        $route_permission->delete();
        return back()->with('success', 'Đã xóa mapping.');
    }
}
