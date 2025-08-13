<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle($request, Closure $next, ...$roles)
    {
        if (! Auth::check()) {
            return redirect()->route('login.form');
        }

        $userRole = Auth::user()->role->name ?? null;

        // Nếu không truyền role nào -> chặn
        if (empty($roles)) {
            abort(403, 'Bạn không có quyền truy cập.');
        }

        // Hỗ trợ nhiều role: role:admin,staff
        if (! in_array($userRole, $roles, true)) {
            abort(403, 'Bạn không có quyền truy cập.');
        }

        return $next($request);
    }
}
