<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\GhnApiService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    protected $apiService;

    public function __construct(GhnApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    public function index(Request $request)
    {
        $query = Order::with('user')->latest();
        $currentStatus = $request->query('status', 'all');
        if ($currentStatus !== 'all') {
            $query->where('status', $currentStatus);
        }
        $orders = $query->paginate(15);
        return view('admins.orders.index', compact('orders', 'currentStatus'));
    }

    public function show(Order $order)
    {
        $order->load(['user', 'items.variant.product.images', 'shipment', 'payment']);
        return view('admins.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,shipped_to_shipper,shipping,delivered,cancelled,received',
        ]);

        if ($request->status === 'received') {
            return back()->with('error', 'Không thể cập nhật trạng thái "Đã nhận". Đây là hành động của khách hàng.');
        }

        $order->status = $request->status;
        $order->save();
        return back()->with('success', 'Đã cập nhật trạng thái đơn hàng thành công!');
    }


    /**
     * Tạo đơn hàng trên hệ thống GHN.
     */
    public function createGhnOrder(Order $order)
    {
        $order->load(['user', 'items.variant.product', 'shipment']);

        if (!$order->shipment || !$order->shipment->district_id || !$order->shipment->ward_code) {
            return back()->with('error', 'Đơn hàng thiếu thông tin địa chỉ (ID Quận/Huyện, Mã Phường/Xã) để gửi đi.');
        }

        $items = $order->items->map(function ($item) {
            return [
                'name'     => $item->variant->product->name,
                'code'     => $item->variant->sku,
                'quantity' => (int) $item->quantity,
                'price'    => (int) $item->price,
                'weight'   => (int) ($item->variant->weight ?? 200),
            ];
        })->toArray();

        $total_weight = $order->items->sum(function ($item) {
            return ($item->variant->weight ?? 200) * $item->quantity;
        });

        $orderData = [
            'client_order_code' => $order->order_code,
            'to_name' => $order->shipment->receiver_name,
            'to_phone' => $order->shipment->phone,
            'to_address' => $order->shipment->address,
            'to_ward_code' => $order->shipment->ward_code,
            'to_district_id' => (int)$order->shipment->district_id,
            'cod_amount' => $order->payment_method === 'cod' ? (int) $order->final_amount : 0,
            'weight' => (int) $total_weight,
            'note' => $order->note,
            'items' => $items,
            'content' => 'Thanh toán đơn hàng ' . $order->order_code,
            'insurance_value' => (int)$order->total_amount,
            'length' => 20,
            'width' => 20,
            'height' => 10,
        ];

        $result = $this->apiService->createOrder($orderData);

        if (isset($result['success']) && $result['success'] === true) {
            $order->shipment->update([
                'tracking_code' => $result['order']['order_code']
            ]);
            $order->update(['status' => 'shipped_to_shipper']);

            return back()->with('success', 'Tạo đơn hàng GHN thành công! Mã vận đơn: ' . $result['order']['order_code']);
        }

        return back()->with('error', 'Tạo đơn hàng GHN thất bại: ' . ($result['message'] ?? 'Lỗi không xác định.'));
    }

    /**
     * Cập nhật thông tin giao hàng cho một đơn hàng (dùng cho admin).
     */
    public function updateShippingInfo(Request $request, Order $order)
    {
        $validated = $request->validate([
            'receiver_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'city' => 'required|string',
            'district' => 'required|string',
            'district_id' => 'required|integer',
            'ward' => 'required|string',
            'ward_code' => 'required|string',
        ]);

        if ($order->shipment) {
            $order->shipment->update($validated);
            return back()->with('success', 'Cập nhật thông tin giao hàng thành công.');
        }

        return back()->with('error', 'Không tìm thấy thông tin giao hàng của đơn hàng này.');
    }
    /**
     * Hủy đơn hàng trên hệ thống GHN.
     */
    public function cancelGhnOrder(Order $order)
    {
        $trackingCode = $order->shipment->tracking_code;
        if (!$trackingCode) {
            return back()->with('error', 'Đơn hàng này chưa có mã vận đơn GHN.');
        }

        $result = $this->apiService->cancelOrder($trackingCode);

        if (isset($result['success']) && $result['success'] === true) {
            $order->shipment->update(['tracking_code' => null]);
            $order->update(['status' => 'processing']);

            return back()->with('success', 'Đã gửi yêu cầu hủy đơn hàng trên GHN.');
        }

        return back()->with('error', 'Hủy đơn hàng GHN thất bại: ' . ($result['message'] ?? 'Lỗi không xác định.'));
    }
}
