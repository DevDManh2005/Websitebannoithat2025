<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class VoucherController extends Controller
{
    public function apply(Request $request)
    {
        $code = $request->input('code');
        $voucher = Voucher::where('code', $code)->first();

        if (!$voucher) {
            return response()->json(['success' => false, 'message' => 'Mã giảm giá không tồn tại.']);
        }

        // === LOGIC KIỂM TRA ĐÃ SỬA LỖI MÚI GIỜ ===
        $now = Carbon::now('Asia/Ho_Chi_Minh'); // Lấy thời gian hiện tại theo giờ Việt Nam
        
        $isExpired = $voucher->end_at && $voucher->end_at->isPast();
        $isNotStarted = $voucher->start_at && $voucher->start_at->isFuture();

        if (!$voucher->is_active || $isNotStarted || $isExpired) {
            return response()->json(['success' => false, 'message' => 'Mã giảm giá không hợp lệ hoặc đã hết hạn.']);
        }
        // === KẾT THÚC SỬA LỖI ===

        if ($voucher->usage_limit !== null && $voucher->used_count >= $voucher->usage_limit) {
            return response()->json(['success' => false, 'message' => 'Mã giảm giá đã hết lượt sử dụng.']);
        }

        $cartItems = Cart::where('user_id', Auth::id())->with('variant')->get();
        $subtotal = $cartItems->sum(callback: function($item) {
            if (!$item->variant) return 0;
            $price = $item->variant->sale_price > 0 && $item->variant->sale_price < $item->variant->price
                ? $item->variant->sale_price
                : $item->variant->price;
            return $price * $item->quantity;
        });

        if ($voucher->min_order_amount > 0 && $subtotal < $voucher->min_order_amount) {
            return response()->json(['success' => false, 'message' => "Đơn hàng phải có giá trị tối thiểu là " . number_format($voucher->min_order_amount) . "đ để áp dụng mã này."]);
        }

        $discount = 0;
        if ($voucher->type == 'percent') {
            $discount = ($subtotal * $voucher->value) / 100;
        } else {
            $discount = $voucher->value;
        }
        
        session()->put('voucher', [
            'code' => $voucher->code,
            'discount' => $discount
        ]);

        return response()->json(['success' => true, 'message' => 'Áp dụng mã giảm giá thành công!', 'discount' => $discount]);
    }

    public function remove()
    {
        session()->forget('voucher');
        return response()->json(['success' => true, 'message' => 'Đã gỡ mã giảm giá.']);
    }
}