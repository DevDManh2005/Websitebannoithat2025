<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectAdminToStaff
{
    public function handle(Request $request, Closure $next): Response
    {
        // Chỉ chạy khi đã đăng nhập & có vai trò staff
        $u = $request->user();
        if ($u && optional($u->role)->name === 'staff') {
            $path = ltrim($request->path(), '/'); // vd: "admin/orders/1/edit"
            if (str_starts_with($path, 'admin/')) {
                // Chuyển "admin/..." -> "staff/..."
                $to = 'staff/'.substr($path, strlen('admin/'));
                $url = $request->getSchemeAndHttpHost().'/'.$to;

                // Giữ nguyên query string (nếu có)
                if ($qs = $request->getQueryString()) {
                    $url .= '?'.$qs;
                }

                // Dùng 307 để giữ nguyên method (POST/PUT/DELETE) + body
                return redirect()->to($url, 307);
            }
        }

        return $next($request);
    }
}
