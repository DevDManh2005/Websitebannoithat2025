<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Models\RoutePermission;

class AutoPermissionMiddleware
{
    public function handle($request, Closure $next)
    {
        $route = $request->route();
        $name  = $route?->getName();

        // Nếu route không có name → bỏ qua
        if (!$name) return $next($request);

        // Tìm mapping
        $map = RoutePermission::where('route_name', $name)->where('is_active', true)->first();

        // Không có mapping → cho qua (tuỳ bạn muốn mặc định chặn hay cho qua)
        if (!$map) return $next($request);

        // Phải đăng nhập
        if (!Auth::check()) {
            return redirect()->route('login.form');
        }

        $user = Auth::user();

        // Kiểm tra quyền: module + action
        if (method_exists($user, 'hasPermission') && $user->hasPermission($map->module_name, $map->action)) {
            return $next($request);
        }

        abort(403, 'Bạn không có quyền truy cập.');
    }
}
