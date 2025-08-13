<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Order;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Gộp quyền (vai trò + gán trực tiếp)
        $rolePerms   = optional($user->role)->permissions ?? collect();
        $directPerms = method_exists($user, 'permissions') ? $user->permissions : collect();
        $allPerms    = $rolePerms->concat($directPerms)->unique('id')
                        ->sortBy([['module_name','asc'], ['action','asc']]);

        // Map nhanh để view dùng
        $can = [
            'orders' => [
                'view'   => $user->hasPermission('orders', 'view'),
                'update' => $user->hasPermission('orders', 'update'),
            ],
            // có thể thêm module khác tương tự...
        ];

        $stats        = [];
        $latestOrders = collect();

        // Thống kê đơn nếu có quyền xem
        if ($can['orders']['view']) {
            $today = Carbon::today();

            $stats['orders'] = [
                'today_count'      => Order::whereDate('created_at', $today)->count(),
                'pending_count'    => Order::where('status', 'pending')->count(),
                'processing_count' => Order::whereIn('status', ['confirmed','processing','ready_to_ship','shipping'])->count(),
                // tổng tiền các đơn giao/nhận xong hôm nay
                'delivered_today'  => Order::whereIn('status', ['delivered','received'])
                    ->whereDate('created_at', $today)
                    ->sum('final_amount'),
            ];

            // LƯU Ý: DB dùng cột order_code + final_amount
            $latestOrders = Order::select('id', 'order_code', 'status', 'final_amount', 'created_at')
                ->latest()
                ->limit(6)
                ->get();
        }

        // Danh sách module user có quyền "view" để render menu/widget
        $viewableModules = $allPerms->where('action', 'view')
            ->groupBy('module_name')->keys()->values();

        // Trả view MỘT lần
        return view('staffs.dashboard', compact(
            'user', 'can', 'stats', 'latestOrders', 'allPerms', 'viewableModules'
        ));
    }
}
