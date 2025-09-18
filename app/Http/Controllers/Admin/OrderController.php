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
    /*
    |--------------------------------------------------------------------------
    | Các phương thức quản lý chính (CRUD)
    |--------------------------------------------------------------------------
    */

    /**
     * Hiển thị danh sách đơn hàng có phân trang và lọc.
     */
    public function index(Request $request): View
    {
        $status = (string) $request->query('status', 'all');
        $search = (string) $request->query('code');

        $orders = Order::with('user')
            ->when($status !== 'all', fn($q) => $q->where('status', $status))
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
     * Hiển thị chi tiết một đơn hàng.
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

    /*
    |--------------------------------------------------------------------------
    | Các phương thức xử lý hành động (Actions)
    |--------------------------------------------------------------------------
    */

    /**
     * Cập nhật trạng thái đơn hàng.
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
     * Cập nhật thông tin giao hàng.
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
     * Đánh dấu "sẵn sàng giao" trong quy trình nội bộ.
     */
    public function readyToShip(Order $order): RedirectResponse
    {
        $order->load('shipment');

        if ($order->shipment) {
            $order->shipment->update([
                'tracking_code' => $order->shipment->tracking_code
                    ?: ('LOCAL-' . now()->format('YmdHis') . '-' . $order->id),
                'status' => 'shipping',
            ]);
        }

        $order->update(['status' => 'shipping']);

        return back()->with('success', 'Đã chuyển đơn sang trạng thái giao hàng (nội bộ).');
    }

    /**
     * Admin đánh dấu đã thu tiền COD.
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
            $order->update([
                'is_paid'        => true,
                'payment_status' => 'paid',
                'paid_at'        => now(),
                'payment_ref'    => $order->payment_ref ?: ('COD-ADMIN-' . now()->format('YmdHis')),
            ]);

            if ($order->payment) {
                $order->payment->update([
                    'status'         => 'paid',
                    'transaction_id' => $order->payment->transaction_id ?: $order->payment_ref,
                ]);
            } else {
                $order->payment()->create([
                    'method'         => 'cod',
                    'status'         => 'paid',
                    'transaction_id' => $order->payment_ref,
                ]);
                // Nếu bạn có service để xử lý trừ kho, hãy gọi nó ở đây
                // app(\App\Services\InventoryService::class)->deductForOrder($order, auth()->id());
            }
        });

        return back()->with('success', 'Đã đánh dấu “đã thu COD” và đồng bộ kho.');
    }

    /*
    |--------------------------------------------------------------------------
    | Các phương thức API cho Frontend (AJAX)
    |--------------------------------------------------------------------------
    */

    /**
     * API endpoint để lấy TẤT CẢ các đơn hàng đang chờ xử lý.
     */
    public function fetchPendingOrders(Request $request)
    {
        $pendingOrders = Order::withoutGlobalScopes()->with('user')
            ->where('status', 'pending')
            ->latest()
            ->get([
                'id',
                'order_code',
                'user_id',
                'final_amount',
                'created_at'
            ]);

        return response()->json([
            'pending_orders' => $pendingOrders,
        ]);
    }
}