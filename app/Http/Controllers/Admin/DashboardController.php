<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // range: 7d | 30d | 90d (mặc định 30d)
        $range = in_array($request->get('range'), ['7d','30d','90d']) ? $request->get('range') : '30d';
        $days  = $range === '7d' ? 7 : ($range === '90d' ? 90 : 30);

        $from = now()->subDays($days - 1)->startOfDay();
        $to   = now()->endOfDay();

        $ordersBase = Order::dateBetween($from, $to);
        $paidBase   = (clone $ordersBase)->paid();

        // KPIs
        $revenue        = (clone $paidBase)->sum(DB::raw('COALESCE(final_amount, total_amount)'));
        $ordersCount    = (clone $ordersBase)->count();
        $paidOrders     = (clone $paidBase)->count();
        $customers      = (clone $ordersBase)->distinct('user_id')->count('user_id');
        $products       = Product::count();
        $aov            = $paidOrders > 0 ? round($revenue / $paidOrders) : 0;

        // Growth vs previous period
        $prevFrom       = $from->copy()->subDays($days)->startOfDay();
        $prevTo         = $from->copy()->subSecond();
        $prevRevenue    = Order::dateBetween($prevFrom, $prevTo)->paid()
                            ->sum(DB::raw('COALESCE(final_amount, total_amount)'));
        $growth         = $prevRevenue > 0 ? round(($revenue - $prevRevenue) * 100 / $prevRevenue, 1) : 0;

        // Recent orders
        $recentOrders = Order::with('user')->latest()->take(8)->get();

        // Sparkline (daily revenue)
        $daily = (clone $paidBase)
            ->selectRaw('DATE(created_at) as d, SUM(COALESCE(final_amount, total_amount)) as revenue')
            ->groupBy('d')->orderBy('d')->get()->keyBy('d');

        $sparkLabels = [];
        $sparkData   = [];
        $cur = $from->copy();
        while ($cur->lte($to)) {
            $key = $cur->toDateString();
            $sparkLabels[] = $cur->format('d/m');
            $sparkData[]   = (int) ($daily[$key]->revenue ?? 0);
            $cur->addDay();
        }

        // Payment donut
        $paymentRows = (clone $paidBase)
            ->select('payment_method', DB::raw('COUNT(*) as cnt'), DB::raw('SUM(COALESCE(final_amount, total_amount)) as revenue'))
            ->groupBy('payment_method')->orderByDesc('revenue')->get();

        $paymentLabels = $paymentRows->pluck('payment_method')->map(fn($v) => $v ?: 'Không rõ')->values();
        $paymentData   = $paymentRows->pluck('revenue')->map(fn($v) => (int)$v)->values();

        // Status funnel
        $statusCounts = (clone $ordersBase)
            ->select('status', DB::raw('COUNT(*) as cnt'))->groupBy('status')->pluck('cnt','status');

        $statusOrder = [
            Order::ST_PENDING,
            Order::ST_PROCESS,
            Order::ST_SHIP_OUT,
            Order::ST_SHIPPING,
            Order::ST_DELIVERED,
            Order::ST_RECEIVED,
            Order::ST_CANCEL,
        ];

        $funnel = [];
        foreach ($statusOrder as $st) {
            $cnt = (int) ($statusCounts[$st] ?? 0);
            $funnel[] = [
                'status' => $st,
                'text'   => Order::getStatusText($st),
                'count'  => $cnt,
                'percent'=> $ordersCount ? round($cnt * 100 / $ordersCount, 1) : 0,
            ];
        }

        // Low stocks
        $lowStocks = ProductVariant::with(['product:id,name','inventory'])
            ->lowStock(5) // dùng scope trong model
            ->orderBy('product_id')->take(6)->get();

        $kpis = [
            'revenue'      => (int) $revenue,
            'orders'       => $ordersCount,
            'paid_orders'  => $paidOrders,
            'customers'    => $customers,
            'products'     => $products,
            'growth'       => $growth,
            'aov'          => (int) $aov,
        ];

        return view('admins.dashboard', [
            'range'          => $range,
            'from'           => $from,
            'to'             => $to,
            'kpis'           => $kpis,
            'recentOrders'   => $recentOrders,
            'sparkLabels'    => $sparkLabels,
            'sparkData'      => $sparkData,
            'paymentLabels'  => $paymentLabels,
            'paymentData'    => $paymentData,
            'funnel'         => $funnel,
            'lowStocks'      => $lowStocks,
        ]);
    }
}
