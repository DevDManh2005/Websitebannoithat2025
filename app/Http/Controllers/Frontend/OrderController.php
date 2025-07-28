<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; // Import DB facade

class OrderController extends Controller
{
    /**
     * Hiển thị danh sách các đơn hàng của người dùng.
     */
    public function index(Request $request)
    {
        $query = Auth::user()->orders()->latest();

        // Lọc theo trạng thái nếu có tham số 'status'
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $orders = $query->paginate(10);
        $currentStatus = $request->status ?? 'all'; // Để truyền trạng thái hiện tại ra view

        return view('frontend.orders.index', compact('orders', 'currentStatus'));
    }

    /**
     * Hiển thị chi tiết một đơn hàng cụ thể.
     */
    public function show(Order $order)
    {
        // Đảm bảo người dùng chỉ có thể xem đơn hàng của chính họ
        if ($order->user_id !== Auth::id()) {
            abort(404);
        }

        // Tải sẵn các thông tin liên quan để tối ưu
        $order->load('items.variant.product.images', 'shipment', 'payment'); // Tải thêm payment

        return view('frontend.orders.show', compact('order'));
    }

    /**
     * Hủy một đơn hàng của người dùng.
     */
    public function cancel(Order $order)
    {
        // Đảm bảo người dùng chỉ có thể hủy đơn hàng của chính họ
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Bạn không có quyền hủy đơn hàng này.');
        }

        if (!$order->isCancellable()) {
            return back()->with('error', 'Đơn hàng này không thể hủy được do trạng thái hiện tại.');
        }

        DB::beginTransaction();
        try {
            $order->status = 'cancelled';
            $order->save();

            // Ghi log audit nếu cần (bạn có thể thêm logic AuditLog ở đây)
            // AuditLog::create([
            //     'user_id' => auth()->id(),
            //     'action' => 'cancel',
            //     'module' => 'order',
            //     'description' => "Người dùng hủy đơn hàng #{$order->order_code}",
            //     'ip_address' => request()->ip(),
            // ]);

            DB::commit();
            return back()->with('success', 'Đơn hàng đã được hủy thành công.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra khi hủy đơn hàng: ' . $e->getMessage());
        }
    }

    /**
     * Đánh dấu đơn hàng đã được khách hàng nhận.
     */
    public function markAsDelivered(Order $order)
    {
        // Đảm bảo người dùng chỉ có thể đánh dấu đơn hàng của chính họ
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Bạn không có quyền thực hiện thao tác này trên đơn hàng này.');
        }

        // Chỉ cho phép đánh dấu đã nhận nếu trạng thái là 'shipped_to_shipper' hoặc 'shipping'
        if (!$order->isReceivableByCustomer()) {
            return back()->with('error', 'Đơn hàng này chưa đủ điều kiện để đánh dấu "Đã nhận hàng".');
        }

        DB::beginTransaction();
        try {
            $order->status = 'delivered';
            $order->save();

            // Ghi log audit nếu cần
            // AuditLog::create([
            //     'user_id' => auth()->id(),
            //     'action' => 'mark_delivered',
            //     'module' => 'order',
            //     'description' => "Người dùng xác nhận đã nhận đơn hàng #{$order->order_code}",
            //     'ip_address' => request()->ip(),
            // ]);

            DB::commit();
            return back()->with('success', 'Bạn đã xác nhận nhận hàng thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra khi xác nhận nhận hàng: ' . $e->getMessage());
        }
    }
}
