<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class PermissionMiddleware
{
    /**
     * Kiểm tra xem user có permission module:action hay không
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     * @param  string                   $module
     * @param  string                   $action
     * @return mixed
     */
    public function handle($request, Closure $next, string $module, string $action)
    {
        if (! Auth::check()) {
            // Chưa đăng nhập
            return redirect()->route('login.form');
        }

        if (! Auth::user()->hasPermission($module, $action)) {
            abort(403, 'Bạn không có quyền thực hiện thao tác này.');
        }

        return $next($request);
    }
}
