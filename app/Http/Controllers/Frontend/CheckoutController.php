<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Inventory; // <-- Thêm dòng này
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Shipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderPlaced;
use App\Http\Controllers\Frontend\PaymentController;

class CheckoutController extends Controller
{
    /**
     * Hiển thị trang thanh toán.
     */
    public function index()
    {
        $user = Auth::user()->load('profile');
        $cartItems = Cart::where('user_id', $user->id)
            ->with('variant.product')
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('home')->with('info', 'Giỏ hàng của bạn đang trống.');
        }

        $totalPrice = 0;
        foreach ($cartItems as $item) {
            $price = $item->variant->sale_price > 0 ? $item->variant->sale_price : $item->variant->price;
            $totalPrice += $price * $item->quantity;
        }

        return view('frontend.checkout.index', compact('user', 'cartItems', 'totalPrice'));
    }

    /**
     * Xử lý việc đặt hàng.
     */
    public function placeOrder(Request $request)
    {
        $request->validate([
            'receiver_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'city' => 'required|string|max:255',
            'district' => 'required|string|max:255',
            'ward' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'payment_method' => 'required|in:cod,vnpay,momo',
            'shipping_fee' => 'required|numeric|min:0',
            'shipping_service_name' => 'required|string',
        ]);

        $user = Auth::user();
        $cartItems = Cart::where('user_id', $user->id)->with('variant.product')->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('home')->with('error', 'Giỏ hàng trống, không thể đặt hàng.');
        }

        DB::beginTransaction();
        try {
            // BƯỚC 1: KIỂM TRA TỒN KHO LẦN CUỐI
            foreach ($cartItems as $item) {
                $inventory = Inventory::where('product_variant_id', $item->product_variant_id)->first();
                $stock = $inventory ? $inventory->quantity : 0;

                if ($item->quantity > $stock) {
                    DB::rollBack(); // Hủy bỏ transaction
                    return redirect()->route('cart.index')->with('error', "Sản phẩm '{$item->variant->product->name}' đã hết hàng hoặc không đủ số lượng. Vui lòng kiểm tra lại giỏ hàng.");
                }
            }

            // BƯỚC 2: TẠO ĐƠN HÀNG (NẾU TẤT CẢ SẢN PHẨM ĐỀU CÒN HÀNG)
            $totalAmount = 0;
            foreach ($cartItems as $item) {
                $price = $item->variant->sale_price > 0 ? $item->variant->sale_price : $item->variant->price;
                $totalAmount += $price * $item->quantity;
            }

            $finalAmount = $totalAmount + $request->shipping_fee;

            $order = Order::create([
                'user_id' => $user->id,
                'order_code' => 'ORD-' . strtoupper(uniqid()),
                'total_amount' => $totalAmount,
                'final_amount' => $finalAmount,
                'status' => 'pending',
                'note' => $request->note,
            ]);

            // BƯỚC 3: TẠO CHI TIẾT ĐƠN HÀNG VÀ TRỪ KHO
            foreach ($cartItems as $item) {
                $price = $item->variant->sale_price > 0 ? $item->variant->sale_price : $item->variant->price;
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_variant_id' => $item->product_variant_id,
                    'quantity' => $item->quantity,
                    'price' => $price,
                    'subtotal' => $price * $item->quantity,
                ]);

                // Trừ kho
                $inventory = Inventory::where('product_variant_id', $item->product_variant_id)->first();
                if ($inventory) {
                    $inventory->quantity -= $item->quantity;
                    $inventory->save();
                }
            }

            // BƯỚC 4: TẠO THÔNG TIN GIAO HÀNG
            Shipment::create([
                'order_id' => $order->id,
                'receiver_name' => $request->receiver_name,
                'phone' => $request->phone,
                'address' => $request->address,
                'city' => $request->city,
                'district' => $request->district,
                'ward' => $request->ward,
                'shipping_fee' => $request->shipping_fee,
            ]);

            // BƯỚC 5: XỬ LÝ THANH TOÁN
            if ($request->payment_method === 'cod') {
                $order->status = 'processing';
                $order->save();
                Cart::where('user_id', $user->id)->delete();
                Mail::to($order->user->email)->send(new OrderPlaced($order));
                DB::commit();
                return redirect()->route('orders.show', $order)->with('success', 'Đặt hàng thành công! Cảm ơn bạn đã mua hàng.');
            
            } elseif ($request->payment_method === 'vnpay') {
                DB::commit();
                return app(PaymentController::class)->createVnpayPayment($order);

            } elseif ($request->payment_method === 'momo') {
                DB::commit();
                return redirect()->route('home')->with('info', 'Chức năng thanh toán MoMo đang được phát triển.');
            }

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Đã có lỗi xảy ra, vui lòng thử lại. Lỗi: ' . $e->getMessage());
        }
    }
}
