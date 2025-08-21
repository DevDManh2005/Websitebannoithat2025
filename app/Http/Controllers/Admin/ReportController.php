<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function dashboard(Request $request)
    {
        // Range mặc định: 30 ngày gần nhất
        $from = $request->filled('from')
            ? Carbon::parse($request->input('from'))->startOfDay()
            : now()->subDays(29)->startOfDay();

        $to = $request->filled('to')
            ? Carbon::parse($request->input('to'))->endOfDay()
            : now()->endOfDay();

        // ====== KPIs ======
        $ordersBase = Order::dateBetween($from, $to);
        $paidBase   = (clone $ordersBase)->paid();

        $revenue         = (clone $paidBase)->sum('final_amount');
        $ordersCount     = (clone $ordersBase)->count();
        $paidOrdersCount = (clone $paidBase)->count();
        $cancelledCount  = (clone $ordersBase)->status(Order::ST_CANCEL)->count();
        $aov             = $paidOrdersCount > 0 ? round($revenue / $paidOrdersCount) : 0;

        // ====== Doanh thu theo ngày ======
        $dailyRevenue = (clone $paidBase)
            ->selectRaw('DATE(created_at) as d, SUM(COALESCE(final_amount, total_amount)) as revenue')
            ->groupBy('d')->orderBy('d')->get();

        $labels = [];
        $series = [];
        $cursor = $from->copy();
        $map = $dailyRevenue->keyBy('d');
        while ($cursor->lte($to)) {
            $key = $cursor->toDateString();
            $labels[] = $cursor->format('d/m');
            $series[] = (int) ($map[$key]->revenue ?? 0);
            $cursor->addDay();
        }

        // ====== Top sản phẩm ======
        $topProducts = OrderItem::query()
            ->select([
                'products.id as product_id',
                'products.name',
                DB::raw('SUM(order_items.quantity) as qty'),
                DB::raw('SUM(COALESCE(order_items.subtotal, order_items.price * order_items.quantity)) as revenue'),
            ])
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->join('product_variants', 'product_variants.id', '=', 'order_items.product_variant_id')
            ->join('products', 'products.id', '=', 'product_variants.product_id')
            ->whereBetween('orders.created_at', [$from, $to])
            ->where(function($q){
                $q->where('orders.is_paid', 1)->orWhere('orders.payment_status', 'paid');
            })
            ->groupBy('products.id','products.name')
            ->orderByDesc('qty')
            ->limit(10)
            ->get();

        // ====== Top danh mục ======
        $topCategories = OrderItem::query()
            ->select([
                'categories.id as category_id',
                'categories.name',
                DB::raw('SUM(order_items.quantity) as qty'),
                DB::raw('SUM(COALESCE(order_items.subtotal, order_items.price * order_items.quantity)) as revenue'),
            ])
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->join('product_variants', 'product_variants.id', '=', 'order_items.product_variant_id')
            ->join('products', 'products.id', '=', 'product_variants.product_id')
            ->join('category_product', 'category_product.product_id', '=', 'products.id')
            ->join('categories', 'categories.id', '=', 'category_product.category_id')
            ->whereBetween('orders.created_at', [$from, $to])
            ->where(function($q){
                $q->where('orders.is_paid', 1)->orWhere('orders.payment_status', 'paid');
            })
            ->groupBy('categories.id','categories.name')
            ->orderByDesc('revenue')
            ->limit(10)
            ->get();

        // ====== Top brand ======
        $topBrands = OrderItem::query()
            ->select([
                'brands.id as brand_id',
                'brands.name',
                DB::raw('SUM(order_items.quantity) as qty'),
                DB::raw('SUM(COALESCE(order_items.subtotal, order_items.price * order_items.quantity)) as revenue'),
            ])
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->join('product_variants', 'product_variants.id', '=', 'order_items.product_variant_id')
            ->join('products', 'products.id', '=', 'product_variants.product_id')
            ->join('brands', 'brands.id', '=', 'products.brand_id')
            ->whereBetween('orders.created_at', [$from, $to])
            ->where(function($q){
                $q->where('orders.is_paid', 1)->orWhere('orders.payment_status', 'paid');
            })
            ->groupBy('brands.id','brands.name')
            ->orderByDesc('revenue')
            ->limit(10)
            ->get();

        // ====== Payment method breakdown ======
        $paymentMethods = (clone $paidBase)
            ->select('payment_method', DB::raw('COUNT(*) as cnt'), DB::raw('SUM(final_amount) as revenue'))
            ->groupBy('payment_method')
            ->orderByDesc('revenue')
            ->get();

        // CHUẨN HÓA DỮ LIỆU CHO CHART
        $paymentLabels = $paymentMethods
            ->pluck('payment_method')
            ->map(fn($v) => $v ?: 'Không rõ')
            ->values()
            ->all();

        $paymentData = $paymentMethods
            ->pluck('revenue')
            ->map(fn($v) => (int)$v)
            ->values()
            ->all();

        // ====== Tồn kho thấp ======
        $lowStocks = ProductVariant::with(['product:id,name','inventory'])
            ->lowStock(5)           // tự phát hiện cột quantity/stock/...
            ->orderBy('product_id')
            ->take(8)
            ->get();

        return view('admins.reports.dashboard', [
            'from' => $from,
            'to' => $to,
            'revenue' => (int) $revenue,
            'ordersCount' => $ordersCount,
            'paidOrdersCount' => $paidOrdersCount,
            'cancelledCount' => $cancelledCount,
            'aov' => (int) $aov,
            'labels' => $labels,
            'series' => $series,
            'topProducts' => $topProducts,
            'topCategories' => $topCategories,
            'topBrands' => $topBrands,
            'paymentMethods' => $paymentMethods,
            'paymentLabels' => $paymentLabels,
            'paymentData' => $paymentData,
            'lowStocks' => $lowStocks,
        ]);
    }

    public function exportTopProductsCsv(Request $request): StreamedResponse
    {
        $from = $request->filled('from') ? Carbon::parse($request->input('from'))->startOfDay() : now()->subDays(29)->startOfDay();
        $to   = $request->filled('to')   ? Carbon::parse($request->input('to'))->endOfDay()   : now()->endOfDay();

        $rows = OrderItem::query()
            ->select([
                'products.id as product_id',
                'products.name',
                DB::raw('SUM(order_items.quantity) as qty'),
                DB::raw('SUM(COALESCE(order_items.subtotal, order_items.price * order_items.quantity)) as revenue'),
            ])
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->join('product_variants', 'product_variants.id', '=', 'order_items.product_variant_id')
            ->join('products', 'products.id', '=', 'product_variants.product_id')
            ->whereBetween('orders.created_at', [$from, $to])
            ->where(function($q){
                $q->where('orders.is_paid', 1)->orWhere('orders.payment_status', 'paid');
            })
            ->groupBy('products.id','products.name')
            ->orderByDesc('qty')
            ->get();

        $filename = 'top-products-'.$from->format('Ymd').'-'.$to->format('Ymd').'.csv';

        return response()->streamDownload(function() use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Product ID','Product Name','Quantity','Revenue']);
            foreach ($rows as $r) {
                fputcsv($out, [$r->product_id, $r->name, (int)$r->qty, (int)$r->revenue]);
            }
            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
