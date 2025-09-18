<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class OrderController extends Controller
{
    /**
     * Quản trị đơn hàng nội bộ (không tích hợp đơn vị vận chuyển bên thứ ba).
     * Chức năng: liệt kê/lọc, xem chi tiết, cập nhật trạng thái, cập nhật địa chỉ,
     * đánh dấu sẵn sàng giao (nội bộ), và đánh dấu đã thu COD.
     */

    /**
     * Danh sách đơn + lọc theo trạng thái.
     */
   public function index(Request $request): View
{
    $status = (string) $request->query('status', 'all');
    // THÊM MỚI: Lấy giá trị từ ô tìm kiếm
    $search = (string) $request->query('code');

    $orders = Order::with('user')
        // Giữ nguyên logic lọc theo trạng thái
        ->when($status !== 'all', fn ($q) => $q->where('status', $status))

        // THÊM MỚI: Logic lọc theo mã đơn/tên khách hàng
        ->when($search, function ($query, $search) {
            $query->where(function ($q) use ($search) {
                $q->where('order_code', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%");
                  });
            });
        })
        
        ->latest()
        ->paginate(15)
        ->appends($request->query());

    return view('admins.orders.index', [
        'orders'        => $orders,
        'currentStatus' => $status,
    ]);
}

    /**
     * Chi tiết đơn: thông tin người dùng, mặt hàng, giao hàng, thanh toán.
     */
    public function show(Order $order): View
    {
        $order->load([
            'user',
            'items.variant.product.images',
            'shipment',
            'payment',
        ]);

        return view('admins.orders.show', compact('order'));
    }

    /**
     * Cập nhật trạng thái đơn (nội bộ).
     * Ghi chú: "received" là do khách hàng xác nhận, admin không set.
     */
    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $request->validate([
            'status' => 'required|in:pending,processing,shipping,delivered,cancelled,received',
        ]);

        if ($request->status === 'received') {
            return back()->with('error', 'Không thể cập nhật trạng thái "Đã nhận". Đây là hành động của khách hàng.');
        }

        $order->update(['status' => $request->status]);

        return back()->with('success', 'Đã cập nhật trạng thái đơn hàng.');
    }

    /**
     * Cập nhật thông tin giao hàng (nội bộ).
     */
    public function updateShippingInfo(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'receiver_name' => 'required|string|max:255',
            'phone'         => 'required|string|max:20',
            'address'       => 'required|string|max:255',
            'city'          => 'required|string',
            'district'      => 'required|string',
            'district_id'   => 'required|integer',
            'ward'          => 'required|string',
            'ward_code'     => 'required|string',
        ]);

        if (!$order->shipment) {
            return back()->with('error', 'Không tìm thấy thông tin giao hàng của đơn hàng này.');
        }

        $order->shipment->update($validated);

        return back()->with('success', 'Đã cập nhật thông tin giao hàng.');
    }

    /**
     * Đánh dấu "sẵn sàng giao" theo quy trình nội bộ:
     * - Tạo/giữ mã tracking nội bộ.
     * - Đổi shipment.status & order.status -> "shipping".
     */
    public function readyToShip(Order $order): RedirectResponse
    {
        $order->load('shipment');

        if ($order->shipment) {
            $order->shipment->update([
                'tracking_code' => $order->shipment->tracking_code
                    ?: ('LOCAL-' . now()->format('YmdHis') . '-' . $order->id),
                'status' => 'shipping', // waiting|shipping|delivered|failed
            ]);
        }

        $order->update(['status' => 'shipping']);

        return back()->with('success', 'Đã chuyển đơn sang trạng thái giao hàng (nội bộ).');
    }

    /**
     * Admin đánh dấu đã THU COD (chỉ cho đơn COD, chưa paid).
     */
   public function markCodPaid(Order $order): RedirectResponse
{
    if (($order->payment_method ?? 'cod') !== 'cod') {
        return back()->with('error', 'Chỉ áp dụng cho đơn thanh toán COD.');
    }
    if (in_array($order->status, ['cancelled', 'received'], true)) {
        return back()->with('error', 'Đơn ở trạng thái hiện tại không thể thu COD.');
    }

    DB::transaction(function () use ($order) {
        // Cập nhật trạng thái tiền của Order
        $order->is_paid        = 1;
        $order->payment_status = 'paid';
        $order->paid_at        = now();
        $order->payment_ref    = $order->payment_ref ?: ('COD-ADMIN-' . now()->format('YmdHis'));
        $order->save();

        // Cập nhật Payment bằng instance để kích hoạt Observer
        $payment = $order->payment; // <-- lấy model instance
        if ($payment) {
            $payment->status         = 'paid';
            $payment->transaction_id = $payment->transaction_id ?: $order->payment_ref;
            $payment->save(); // <-- sẽ kích hoạt PaymentObserver -> deductForOrder()
        } else {
            // Phòng trường hợp hiếm: chưa có payment record
            $payment = $order->payment()->create([
                'method'         => 'cod',
                'status'         => 'paid',
                'transaction_id' => $order->payment_ref,
            ]);

            // Vì Observer chỉ lắng nghe "updated", còn đây là "created",
            // ta gọi service trừ kho trực tiếp (idempotent nên an toàn):
            app(\App\Services\InventoryService::class)->deductForOrder($order, auth()->id());
        }
    });

    return back()->with('success', 'Đã đánh dấu “đã thu COD” và đồng bộ kho.');
}
}
class OrderPulseController extends Controller
{
    public function __invoke(Request $request)
    {
        // since: có thể là ms, s, hoặc ISO8601. Không có thì mặc định 60s gần nhất.
        $sinceParam = $request->query('since');
        $limit      = (int) $request->query('limit', 6);

        if ($sinceParam === null || $sinceParam === '') {
            $since = now()->subSeconds(60);
        } elseif (ctype_digit((string) $sinceParam)) {
            $ts    = (int) $sinceParam;
            $since = Carbon::createFromTimestamp($ts > 1_000_000_000_000 ? (int) floor($ts / 1000) : $ts);
        } else {
            $since = Carbon::parse($sinceParam);
        }

        // Lấy đơn mới hơn mốc 'since'
        $orders = Order::with('user')
            ->where('created_at', '>', $since)
            ->latest('id')
            ->limit($limit)
            ->get();

        return response()->json([
            'now_ts'    => now()->valueOf(), // ms
            'new_count' => $orders->count(),
            'items'     => $orders->map(function ($o) {
                return [
                    'id'    => $o->id,
                    'code'  => (string) $o->order_code,
                    'user'  => optional($o->user)->name ?? 'Khách',
                    'total' => number_format($o->final_amount) . ' ₫',
                    'when'  => optional($o->created_at)->format('H:i d/m'),
                    'url'   => route('admin.orders.show', $o),
                ];
            }),
        ]);
    }
     public function fetchNewOrders(Request $request)
    {
        $request->validate([
            'last_check' => 'required|date_format:Y-m-d\TH:i:s.u\Z',
        ]);

        $lastCheckTime = $request->query('last_check');

        $newOrders = Order::with('user:id,name')
            ->where('created_at', '>', $lastCheckTime)
            ->latest() // Sắp xếp để đơn mới nhất ở đầu
            ->get([
                'id',
                'order_code',
                'user_id',
                'final_amount',
                'created_at'
            ]);

        return response()->json([
            'new_orders' => $newOrders,
            'server_time' => now()->toIso8601String(), // Trả về thời gian server để client đồng bộ
        ]);
    }
}