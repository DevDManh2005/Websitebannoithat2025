<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
   public function handle($request, Closure $next, $role)
{
    if (! Auth::check()) {
        return redirect()->route('login.form');
    }

    // Sử dụng trường 'name' (admin|staff|user), không phải slug
    if (Auth::user()->role->name !== $role) {
        abort(403, 'Bạn không có quyền truy cập.');
    }

    return $next($request);
}
}
