<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Models\RoutePermission;

class AutoPermissionByRoute
{
    public function handle($request, Closure $next)
    {
        $user = Auth::user();
        if (! $user) {
            return redirect()->route('login.form');
        }

        $routeName = optional($request->route())->getName();
        if (! $routeName) return $next($request);

        $rp = RoutePermission::where('route_name', $routeName)
            ->where('is_active', 1)
            ->first();

        // Không mapping => cho qua (mặc định các trang không yêu cầu quyền)
        if (! $rp || !$rp->module_name || !$rp->action) {
            return $next($request);
        }

        // Kiểm tra quyền (ưu tiên gán trực tiếp rồi đến theo role)
        if ($user->hasPermission($rp->module_name, $rp->action)) {
            return $next($request);
        }

        abort(403, 'Bạn không có quyền truy cập.');
    }
}
