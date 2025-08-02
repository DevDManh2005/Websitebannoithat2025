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

class CheckoutController extends Controller
{
    public function index()
    {
        $user = Auth::user()->load('profile');
        $cartItems = Cart::where('user_id', $user->id)->with('variant.product')->get();
        
        if($cartItems->isEmpty()){
            return redirect()->route('home')->with('error', 'Giỏ hàng của bạn đang trống.');
        }

        $subtotal = $cartItems->sum(function ($item) {
            $price = $item->variant->sale_price > 0 && $item->variant->sale_price < $item->variant->price
                ? $item->variant->sale_price
                : $item->variant->price;
            return $price * $item->quantity;
        });

        // Lấy thông tin voucher từ session nếu có
        $voucher = session()->get('voucher');
        $discount = $voucher['discount'] ?? 0;
        $voucherCode = $voucher['code'] ?? null;

        return view('frontend.checkout.index', compact('user', 'cartItems', 'subtotal', 'discount', 'voucherCode'));
    }

    public function placeOrder(Request $request)
    {
        $validated = $request->validate([
            'receiver_name' => 'required|string|max:255',
            'phone'         => 'required|string|max:20',
            'city'          => 'required|string',
            'district'      => 'required|string',
            'district_id'   => 'required|integer',
            'ward'          => 'required|string',
            'ward_code'     => 'required|string',
            'address'       => 'required|string|max:255',
            'note'          => 'nullable|string',
            'shipping_fee'  => 'required|numeric|min:0',
            'payment_method'=> 'required|in:cod,vnpay,momo',
        ]);
        
        $user = Auth::user();
        $cartItems = Cart::where('user_id', $user->id)->with('variant')->get();
        if ($cartItems->isEmpty()) {
            return redirect()->route('home')->with('error', 'Giỏ hàng của bạn đang trống.');
        }

        $order = null;
        DB::beginTransaction();
        try {
            $subtotal = $cartItems->sum(function ($item) {
                $price = $item->variant->sale_price > 0 ? $item->variant->sale_price : $item->variant->price;
                return $price * $item->quantity;
            });
            
            $voucherData = session()->get('voucher');
            $discount = $voucherData['discount'] ?? 0;

            $finalAmount = ($subtotal - $discount) + $validated['shipping_fee'];
            // Đảm bảo tổng tiền không bao giờ âm
            $finalAmount = $finalAmount < 0 ? 0 : $finalAmount;

            $order = Order::create([
                'user_id'      => $user->id,
                'order_code'   => 'ORD-' . strtoupper(Str::random(10)),
                'total_amount' => $subtotal,
                'discount'     => $discount,
                'final_amount' => $finalAmount,
                'note'         => $validated['note'],
                'status'       => 'pending',
                'payment_method' => $validated['payment_method'],
            ]);

            foreach ($cartItems as $cartItem) {
                OrderItem::create([
                    'order_id'           => $order->id,
                    'product_variant_id' => $cartItem->product_variant_id,
                    'quantity'           => $cartItem->quantity,
                    'price'              => $cartItem->variant->sale_price > 0 ? $cartItem->variant->sale_price : $cartItem->variant->price,
                    'subtotal'           => ($cartItem->variant->sale_price > 0 ? $cartItem->variant->sale_price : $cartItem->variant->price) * $cartItem->quantity,
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
                $voucher = Voucher::where('code', $voucherData['code'])->first();
                if ($voucher) {
                    $voucher->increment('used_count');
                }
            }

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Place order failed: " . $e->getMessage());
            return back()->with('error', 'Đã có lỗi xảy ra khi tạo đơn hàng, vui lòng thử lại.')->withInput();
        }
        
        session()->forget('voucher');
        Cart::where('user_id', $user->id)->delete();
        
        if ($validated['payment_method'] === 'vnpay') {
            return redirect()->route('payment.vnpay.create', ['order' => $order->id]);
        }
        
        return redirect()->route('orders.show', $order->id)->with('success', 'Đặt hàng thành công!');
    }
}