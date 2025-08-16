<?php

use Illuminate\Support\Facades\Route;

if (! function_exists('area_prefix')) {
    function area_prefix(): string {
        $name = optional(request()->route())->getName() ?? '';
        return str_starts_with($name, 'staff.') ? 'staff' : 'admin';
    }
}

if (! function_exists('area_route')) {
    /**
     * Gọi route theo khu hiện tại: area_route('orders.index', ['id'=>...])
     */
    function area_route(string $name, array $params = [], bool $absolute = true): string {
        $prefix = area_prefix();
        $full   = $prefix.'.'.$name;

        // Nếu thiếu route theo prefix hiện tại, fallback prefix còn lại (an toàn khi tái dùng view)
        if (!Route::has($full)) {
            $alt = ($prefix === 'staff' ? 'admin' : 'staff').'.'.$name;
            if (Route::has($alt)) $full = $alt;
        }

        return route($full, $params, $absolute);
    }
}
