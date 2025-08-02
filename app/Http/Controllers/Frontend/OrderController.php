<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Auth::user()->orders()->latest();
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }
        $orders = $query->paginate(10);
        $currentStatus = $request->status ?? 'all';
        return view('frontend.orders.index', compact('orders', 'currentStatus'));
    }

    public function show(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(404);
        }
        $order->load(['items.variant.product.images', 'shipment', 'payment']);
        return view('frontend.orders.show', compact('order'));
    }

    public function cancel(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Bạn không có quyền hủy đơn hàng này.');
        }

        if (!$order->isCancellable()) {
            return back()->with('error', 'Đơn hàng này không thể hủy được do trạng thái hiện tại.');
        }

        $order->update(['status' => 'cancelled']);
        return back()->with('success', 'Đơn hàng đã được hủy thành công.');
    }

    public function markAsReceived(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }
        
        if (!$order->isReceivableByCustomer()) {
            return back()->with('error', 'Đơn hàng này chưa thể xác nhận đã nhận.');
        }

        $order->update(['status' => 'received']);
        return back()->with('success', 'Bạn đã xác nhận nhận hàng thành công!');
    }
}