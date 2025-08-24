<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Shipment;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Http\Requests\AddressRequest;

class CheckoutController extends Controller
{
    /**
     * Hiển thị trang thanh toán với các sản phẩm đã được chọn.
     */
    public function index()
    {
        $user = Auth::user()->load('profile');
        
        //Chỉ lấy các sản phẩm đã được chọn trong giỏ hàng
        $cartItems = Cart::where('user_id', $user->id)
            ->where('is_selected', true) // DÒNG QUAN TRỌNG
            ->with('variant.product')
            ->get();
        
        if($cartItems->isEmpty()){
            return redirect()->route('cart.index')->with('error', 'Bạn chưa chọn sản phẩm nào để thanh toán. Vui lòng quay lại giỏ hàng.');
        }

        $subtotal = $cartItems->sum(function ($item) {
            $price = $item->variant->sale_price ?: $item->variant->price;
            return $price * $item->quantity;
        });

        $voucher = session()->get('voucher');
        $discount = $voucher['discount'] ?? 0;
        $voucherCode = $voucher['code'] ?? null;

        $total = $subtotal - $discount;
        $total = $total < 0 ? 0 : $total;

        return view('frontend.checkout.index', compact(
            'user', 
            'cartItems', 
            'subtotal', 
            'discount', 
            'voucherCode',
            'total'
        ));
    }

    /**
     * Xử lý đặt hàng.
     */
    public function placeOrder(AddressRequest $request)
    {
        $validated = $request->validated();

        $user = Auth::user();
        // Chỉ xử lý các sản phẩm đã được chọn
        $cartItems = Cart::where('user_id', $user->id)
            ->where('is_selected', true)
            ->with('variant')
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Không có sản phẩm nào được chọn để đặt hàng.');
        }

        $order = null;
        DB::beginTransaction();
        try {
            $subtotal = $cartItems->sum(function ($item) {
                $price = $item->variant->sale_price ?: $item->variant->price;
                return $price * $item->quantity;
            });
            
            $voucherData = session()->get('voucher');
            $discount = $voucherData['discount'] ?? 0;

            $finalAmount = ($subtotal - $discount) + $validated['shipping_fee'];
            $finalAmount = $finalAmount < 0 ? 0 : $finalAmount;

            $order = Order::create([
                'user_id'       => $user->id,
                'order_code'    => 'ORD-' . strtoupper(Str::random(10)),
                'total_amount'  => $subtotal,
                'discount'      => $discount,
                'final_amount'  => $finalAmount,
                'note'          => $validated['note'],
                'status'        => 'pending',
                'payment_method' => $validated['payment_method'],
            ]);

            foreach ($cartItems as $cartItem) {
                OrderItem::create([
                    'order_id'           => $order->id,
                    'product_variant_id' => $cartItem->product_variant_id,
                    'quantity'           => $cartItem->quantity,
                    'price'              => $cartItem->variant->sale_price ?: $cartItem->variant->price,
                    'subtotal'           => ($cartItem->variant->sale_price ?: $cartItem->variant->price) * $cartItem->quantity,
                ]);
            }

            Shipment::create([
                'order_id'      => $order->id,
                'receiver_name' => $validated['receiver_name'],
                'phone'         => $validated['phone'],
                'address'       => $validated['address'],
                'city'          => $validated['city'],
                'district'      => $validated['district'],
                'district_id'   => $validated['district_id'],
                'ward'          => $validated['ward'],
                'ward_code'     => $validated['ward_code'],
                'shipping_fee'  => $validated['shipping_fee'],
            ]);

            Payment::create([
                'order_id' => $order->id,
                'method'   => $validated['payment_method'],
                'status'   => 'unpaid',
            ]);

            if ($voucherData) {
                Voucher::where('code', $voucherData['code'])->first()?->increment('used_count');
            }

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Place order failed: " . $e->getMessage());
            return back()->with('error', 'Đã có lỗi xảy ra khi tạo đơn hàng, vui lòng thử lại.')->withInput();
        }
        
        // Chỉ xóa những sản phẩm đã được chọn (đã được đặt hàng)
        Cart::where('user_id', $user->id)->where('is_selected', true)->delete();
        session()->forget('voucher');
        
        if ($validated['payment_method'] === 'vnpay') {
            return redirect()->route('payment.vnpay.create', ['order' => $order->id]);
        }
        
        return redirect()->route('orders.show', $order->id)->with('success', 'Đặt hàng thành công!');
    }
}
