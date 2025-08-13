<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RoleAnyMiddleware
{
    public function handle($request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            return redirect()->route('login.form');
        }

        $userRole = optional(Auth::user()->role)->name;
        if (!$userRole || !in_array($userRole, $roles, true)) {
            abort(403, 'Bạn không có quyền truy cập.');
        }
        return $next($request);
    }
}
