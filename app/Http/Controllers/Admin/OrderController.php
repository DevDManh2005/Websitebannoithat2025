<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\GhtkApiService; // <-- Thêm dòng này
use Illuminate\Http\Request;

class OrderController extends Controller
{
    protected $ghtkApiService;

    public function __construct(GhtkApiService $ghtkApiService)
    {
        $this->ghtkApiService = $ghtkApiService;
    }

    /**
     * Hiển thị danh sách tất cả đơn hàng.
     */
    public function index()
    {
        $orders = Order::with('user')->latest()->paginate(15);
        return view('admins.orders.index', compact('orders'));
    }

    /**
     * Hiển thị chi tiết một đơn hàng.
     */
    public function show(Order $order)
    {
        $order->load(['user', 'items.variant.product', 'shipment']);
        return view('admins.orders.show', compact('order'));
    }

    /**
     * Cập nhật trạng thái của một đơn hàng.
     */
    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled',
        ]);

        $order->status = $request->status;
        $order->save();

        return back()->with('success', 'Đã cập nhật trạng thái đơn hàng thành công!');
    }

    /**
     * Tạo đơn hàng trên hệ thống GHTK.
     */
    public function createGhtkOrder(Order $order)
    {
        $order->load(['user', 'items.variant.product', 'shipment']);

        // Chuẩn bị dữ liệu sản phẩm
        $products = $order->items->map(function ($item) {
            return [
                'name'     => $item->variant->product->name,
                'weight'   => 0.2, // Giả định mỗi sản phẩm nặng 0.2kg
                'quantity' => $item->quantity,
            ];
        })->toArray();
        
        // Chuẩn bị dữ liệu đơn hàng
        $orderData = [
            'order_code'    => $order->order_code,
            'receiver_name' => $order->shipment->receiver_name,
            'phone'         => $order->shipment->phone,
            'address'       => $order->shipment->address,
            'city'          => $order->shipment->city,
            'district'      => $order->shipment->district,
            'ward'          => $order->shipment->ward,
            'note'          => $order->note,
            'user_email'    => $order->user->email,
            'payment_method'=> $order->payment_method, // Cần thêm trường này vào Order model
            'total_amount'  => $order->total_amount,
            'final_amount'  => $order->final_amount,
        ];

        $result = $this->ghtkApiService->createOrder($orderData, $products);

        if (isset($result['success']) && $result['success'] === true) {
            // Cập nhật mã vận đơn vào DB
            $order->shipment->update([
                'tracking_code' => $result['order']['label']
            ]);
            // Cập nhật trạng thái đơn hàng
            $order->update(['status' => 'shipped']);

            return back()->with('success', 'Tạo đơn hàng GHTK thành công! Mã vận đơn: ' . $result['order']['label']);
        }

        return back()->with('error', 'Tạo đơn hàng GHTK thất bại: ' . ($result['message'] ?? 'Lỗi không xác định.'));
    }

    /**
     * Hủy đơn hàng trên hệ thống GHTK.
     */
    public function cancelGhtkOrder(Order $order)
    {
        $trackingCode = $order->shipment->tracking_code;

        if (!$trackingCode) {
            return back()->with('error', 'Đơn hàng này chưa có mã vận đơn GHTK.');
        }

        $result = $this->ghtkApiService->cancelOrder($trackingCode);

        if (isset($result['success']) && $result['success'] === true) {
            // Xóa mã vận đơn khỏi DB
            $order->shipment->update(['tracking_code' => null]);
            // Cập nhật lại trạng thái đơn hàng (ví dụ: quay về 'processing')
            $order->update(['status' => 'processing']);

            return back()->with('success', 'Đã hủy đơn hàng trên GHTK thành công.');
        }
        
        return back()->with('error', 'Hủy đơn hàng GHTK thất bại: ' . ($result['message'] ?? 'Lỗi không xác định.'));
    }
}
