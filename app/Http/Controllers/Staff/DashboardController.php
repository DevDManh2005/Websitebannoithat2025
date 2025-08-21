<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

// Models có thật trong app bạn
use App\Models\Order;

class DashboardController extends Controller
{
    public function index()
    {
        $user     = Auth::user();
        $modules  = config('staff_modules');

        // Shortcuts tự sinh từ config + lọc theo quyền "view"
        $shortcuts = collect($modules)->map(function($def, $key){
            return ['name' => $def['label'], 'route' => $def['index'] ?? null, 'module' => $key];
        })->values()->all();

        $widgets = [];

        /* ================== ORDERS ================== */
        if ($user->hasPermission('orders', 'view')) {
            $today = Carbon::today();

            $widgets['orders'] = [
                'title'  => $modules['orders']['label'],
                'icon'   => $modules['orders']['icon'],
                'route'  => $modules['orders']['index'],
                'stats'  => [
                    'Hôm nay (số đơn)'       => Order::whereDate('created_at', $today)->count(),
                    'Đang chờ'               => Order::where('status', 'pending')->count(),
                    'Đang xử lý / giao'      => Order::whereIn('status', ['confirmed','processing','ready_to_ship','shipping'])->count(),
                    'Doanh thu giao xong hôm nay'
                                              => Order::whereIn('status', ['delivered','received'])
                                                      ->whereDate('created_at', $today)
                                                      ->sum('final_amount'),
                ],
                'recent' => Order::select(['id','order_code','status','final_amount','created_at'])
                                  ->latest()->limit(6)->get()
                                  ->map(function($o){
                                      return [
                                          'Mã đơn'    => $o->order_code,
                                          'Trạng thái'=> $o->status,
                                          'Số tiền'   => number_format($o->final_amount,0,',','.').'₫',
                                          'Ngày tạo'  => $o->created_at->format('d/m/Y H:i'),
                                      ];
                                  }),
            ];
        }

        /* ================== PRODUCTS ================== */
        if ($user->hasPermission('products', 'view') && class_exists(\App\Models\Product::class)) {
            $Product = \App\Models\Product::query();

            $total = $Product->count();

            // Tìm cột trạng thái khả dụng để đếm "Đang bán"
            $active = null;
            if (Schema::hasColumn('products', 'is_active')) {
                $active = \App\Models\Product::where('is_active', 1)->count();
            } elseif (Schema::hasColumn('products', 'status')) {
                // hỗ trợ status=1 hoặc status='active'
                $active = \App\Models\Product::where('status', 1)->count();
                if ($active === 0) {
                    $active = \App\Models\Product::where('status', 'active')->count();
                }
            }

            // Cột hiển thị gần đây (chỉ lấy cột có thật)
            $cols = array_values(array_filter([
                Schema::hasColumn('products','name')        ? 'name'        : null,
                Schema::hasColumn('products','sku')         ? 'sku'         : null,
                Schema::hasColumn('products','price')       ? 'price'       : null,
                Schema::hasColumn('products','status')      ? 'status'      : (Schema::hasColumn('products','is_active') ? 'is_active' : null),
                'created_at',
            ]));

            $recent = \App\Models\Product::select(array_merge(['id'], $cols))
                        ->latest()->limit(6)->get()->map(function($p) use ($cols){
                            $row = [];
                            foreach ($cols as $c) {
                                $label = ucfirst(str_replace('_',' ', $c));
                                $val   = $p->{$c};
                                if ($c === 'price') $val = number_format((float)$val,0,',','.').'₫';
                                if ($c === 'is_active') $val = $val ? 'Đang bán' : 'Ẩn';
                                if ($c === 'created_at') $val = optional($p->created_at)->format('d/m/Y H:i');
                                $row[$label] = $val;
                            }
                            return $row;
                        });

            $stats = ['Tổng sản phẩm' => $total];
            if (!is_null($active)) $stats['Đang bán'] = $active;

            $widgets['products'] = [
                'title'  => $modules['products']['label'],
                'icon'   => $modules['products']['icon'],
                'route'  => $modules['products']['index'],
                'stats'  => $stats,
                'recent' => $recent,
            ];
        }

        /* ================== VOUCHERS ================== */
        if ($user->hasPermission('vouchers', 'view') && class_exists(\App\Models\Voucher::class)) {
            $Voucher = \App\Models\Voucher::query();
            $total   = $Voucher->count();

            $today   = Carbon::today();

            $valid   = null; $expired = null;
            if (Schema::hasColumn('vouchers', 'end_date')) {
                $q = \App\Models\Voucher::query();
                if (Schema::hasColumn('vouchers','is_active')) $q->where('is_active',1);
                $valid = $q->whereDate('end_date','>=',$today)->count();

                $expired = \App\Models\Voucher::whereDate('end_date','<',$today)->count();
            }

            $cols = array_values(array_filter([
                'code',
                Schema::hasColumn('vouchers','discount')      ? 'discount'      : null,
                Schema::hasColumn('vouchers','discount_type') ? 'discount_type' : null,
                Schema::hasColumn('vouchers','end_date')      ? 'end_date'      : null,
                'created_at',
            ]));

            $recent = \App\Models\Voucher::select(array_merge(['id'], $cols))
                ->latest()->limit(6)->get()->map(function($v) use ($cols){
                    $row = [];
                    foreach ($cols as $c) {
                        $label = ucfirst(str_replace('_',' ', $c));
                        $val   = $v->{$c};
                        if (in_array($c, ['end_date','created_at'])) $val = optional($val)->format('d/m/Y');
                        $row[$label] = $val;
                    }
                    return $row;
                });

            $stats = ['Tổng voucher' => $total];
            if (!is_null($valid))   $stats['Còn hạn'] = $valid;
            if (!is_null($expired)) $stats['Hết hạn'] = $expired;

            $widgets['vouchers'] = [
                'title'  => $modules['vouchers']['label'],
                'icon'   => $modules['vouchers']['icon'],
                'route'  => $modules['vouchers']['index'],
                'stats'  => $stats,
                'recent' => $recent,
            ];
        }

        /* ================== REVIEWS ================== */
        if ($user->hasPermission('reviews', 'view') && class_exists(\App\Models\ProductReview::class)) {
            $Review = \App\Models\ProductReview::query();
            $total  = $Review->count();

            $pending = $approved = null;
            if (Schema::hasColumn('product_reviews','status')) {
                $pending  = \App\Models\ProductReview::where('status','pending')->count();
                $approved = \App\Models\ProductReview::where('status','approved')->count();
            }

            $cols = array_values(array_filter([
                Schema::hasColumn('product_reviews','user_id')    ? 'user_id' : null,
                Schema::hasColumn('product_reviews','product_id') ? 'product_id' : null,
                Schema::hasColumn('product_reviews','rating')     ? 'rating' : null,
                Schema::hasColumn('product_reviews','status')     ? 'status' : null,
                'created_at',
            ]));

            $recent = \App\Models\ProductReview::select(array_merge(['id'], $cols))
                ->latest()->limit(6)->get()->map(function($r) use ($cols){
                    $row = [];
                    foreach ($cols as $c) {
                        $label = ucfirst(str_replace('_',' ', $c));
                        $val   = $r->{$c};
                        if ($c === 'created_at') $val = optional($r->created_at)->format('d/m/Y H:i');
                        $row[$label] = $val;
                    }
                    return $row;
                });

            $stats = ['Tổng đánh giá' => $total];
            if (!is_null($pending))  $stats['Chờ duyệt'] = $pending;
            if (!is_null($approved)) $stats['Đã duyệt']  = $approved;

            $widgets['reviews'] = [
                'title'  => $modules['reviews']['label'],
                'icon'   => $modules['reviews']['icon'],
                'route'  => $modules['reviews']['index'],
                'stats'  => $stats,
                'recent' => $recent,
            ];
        }

        /* ================== SUPPORT TICKETS ================== */
        if ($user->hasPermission('support-tickets', 'view') && class_exists(\App\Models\SupportTicket::class)) {
            $Ticket = \App\Models\SupportTicket::query();
            $total  = $Ticket->count();

            $new = $inProgress = $closed = null;
            if (Schema::hasColumn('support_tickets','status')) {
                $new        = \App\Models\SupportTicket::where('status','new')->count();
                $inProgress = \App\Models\SupportTicket::where('status','in_progress')->count();
                $closed     = \App\Models\SupportTicket::where('status','closed')->count();
            }

            $cols = array_values(array_filter([
                'subject',
                Schema::hasColumn('support_tickets','priority') ? 'priority' : null,
                Schema::hasColumn('support_tickets','status')   ? 'status'   : null,
                'created_at',
            ]));

            $recent = \App\Models\SupportTicket::select(array_merge(['id'], $cols))
                ->latest()->limit(6)->get()->map(function($t) use ($cols){
                    $row = [];
                    foreach ($cols as $c) {
                        $label = ucfirst(str_replace('_',' ', $c));
                        $val   = $t->{$c};
                        if ($c === 'created_at') $val = optional($t->created_at)->format('d/m/Y H:i');
                        $row[$label] = $val;
                    }
                    return $row;
                });

            $stats = ['Tổng ticket' => $total];
            if (!is_null($new))        $stats['Mới']         = $new;
            if (!is_null($inProgress)) $stats['Đang xử lý']  = $inProgress;
            if (!is_null($closed))     $stats['Đã đóng']     = $closed;

            $widgets['support-tickets'] = [
                'title'  => $modules['support-tickets']['label'],
                'icon'   => $modules['support-tickets']['icon'],
                'route'  => $modules['support-tickets']['index'],
                'stats'  => $stats,
                'recent' => $recent,
            ];
        }

        /* Danh sách quyền để hiển thị */
        $allPerms = $user->allPermissions()->sortBy([['module_name','asc'], ['action','asc']]);

        return view('staffs.dashboard', compact('user', 'widgets', 'shortcuts', 'allPerms'));
    }
}
