<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\AddressRequest;
class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Auth::user()->orders()->latest();

        $status = $request->query('status', 'all');
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $orders = $query->paginate(10);
        $currentStatus = $status;

        return view('frontend.orders.index', compact('orders', 'currentStatus'));
    }

    public function show(Order $order)
    {
        $u = auth()->user();
        $isOwner = $u && ((int)$u->id === (int)$order->user_id);
        $role    = optional($u->role)->name;
        $isStaff = in_array($role, ['admin', 'staff'], true);

        abort_unless($isOwner || $isStaff, 404);
        return view('frontend.orders.show', compact('order'));
    }

    /**
     * Hủy đơn hàng (chỉ khi pending | processing).
     */
    public function cancel(Order $order)
    {
        $u = auth()->user();
        $isOwner = $u && ((int)$u->id === (int)$order->user_id);
        $role    = optional($u->role)->name;
        $isStaff = in_array($role, ['admin', 'staff'], true);

        abort_unless($isOwner || $isStaff, 403, 'Bạn không có quyền hủy đơn hàng này.');

        // chỉ hủy khi còn cho phép (pending|processing)
        if (!in_array($order->status, ['pending', 'processing'], true)) {
            return back()->with('error', 'Đơn hàng không thể hủy ở trạng thái hiện tại.');
        }

        $order->update(['status' => 'cancelled']);

        return back()->with('success', 'Đã hủy đơn hàng thành công.');
    }

    /**
     * Khách xác nhận đã nhận hàng (chỉ khi delivered).
     * - Nếu COD: đánh dấu đã thanh toán.
     */
    public function receive(Order $order)
    {
        $u = auth()->user();
        $isOwner = $u && ((int)$u->id === (int)$order->user_id);
        $role    = optional($u->role)->name;
        $isStaff = in_array($role, ['admin', 'staff'], true);

        // Cho phép nếu là chủ đơn hàng HOẶC là nhân viên
        abort_unless($isOwner || $isStaff, 403, 'Bạn không có quyền xác nhận đơn hàng này.');

        if ($order->status !== 'delivered') {
            return back()->with('error', 'Đơn hàng chưa ở trạng thái có thể xác nhận đã nhận.');
        }

        DB::transaction(function () use ($order) {
            // Đánh dấu đã nhận
            $order->update(['status' => 'received']);

            // Nếu COD mà chưa paid, coi như đã thu khi khách xác nhận
            if (($order->payment_method ?? 'cod') === 'cod') {
                $order->update([
                    'is_paid'        => 1,
                    'payment_status' => 'paid',
                    'paid_at'        => now(),
                    'payment_ref'    => $order->payment_ref ?: ('COD-USER-' . now()->format('YmdHis')),
                ]);

                if ($order->payment()->exists()) {
                    $order->payment()->update([
                        'status'     => 'paid',
                        'updated_at' => now(),
                    ]);
                }
            }
        });

        return back()->with('success', 'Xác nhận đã nhận hàng thành công. Cảm ơn bạn!');
    }

    /**
     * Cập nhật thông tin địa chỉ giao hàng (chỉ khi pending | processing).
     */
    public function updateAddress(AddressRequest $request, Order $order)
    {
        $u = auth()->user();
        $isOwner = $u && ((int)$u->id === (int)$order->user_id);
        $role    = optional($u->role)->name;
        $isStaff = in_array($role, ['admin', 'staff'], true);

        // Cho phép nếu là chủ đơn hàng HOẶC là nhân viên
        abort_unless($isOwner || $isStaff, 403, 'Bạn không có quyền xác nhận đơn hàng này.');

        if (!in_array($order->status, ['pending', 'processing'], true)) {
            return back()->with('error', 'Đơn hàng không thể chỉnh sửa ở trạng thái hiện tại.');
        }

        $validated = $request->validated();

        if ($order->shipment) {
            $order->shipment->update($validated);
        }

        return back()->with('success', 'Thông tin địa chỉ đã được cập nhật thành công.');
    }
}
