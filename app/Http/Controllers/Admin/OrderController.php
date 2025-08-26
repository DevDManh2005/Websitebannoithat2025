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
            $order->update([
                'is_paid'        => 1,
                'payment_status' => 'paid',
                'paid_at'        => now(),
                'payment_ref'    => $order->payment_ref ?: ('COD-ADMIN-' . now()->format('YmdHis')),
            ]);

            if ($order->payment()->exists()) {
                $order->payment()->update([
                    'status'    => 'paid',
                    'updated_at'=> now(),
                ]);
            }
        });

        return back()->with('success', 'Đã đánh dấu “đã thu COD”.');
    }
}
