<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderPlaced;

class PaymentController extends Controller
{
    /**
     * Chuyển hướng người dùng đến cổng VNPAY để thanh toán.
     */
    public function createVnpayPayment(Order $order)
    {
        $vnp_TmnCode = env('VNP_TMNCODE');
        $vnp_HashSecret = env('VNP_HASHSECRET');
        $vnp_Url = env('VNP_URL');
        $vnp_Returnurl = env('VNP_RETURNURL');
        
        $vnp_TxnRef = $order->order_code;
        $vnp_OrderInfo = "Thanh toan don hang " . $order->order_code;
        $vnp_OrderType = 'billpayment';
        $vnp_Amount = $order->final_amount * 100;
        $vnp_Locale = 'vn';
        $vnp_BankCode = 'NCB';
        $vnp_IpAddr = request()->ip();

        $inputData = array(
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $vnp_Returnurl,
            "vnp_TxnRef" => $vnp_TxnRef,
        );

        if (isset($vnp_BankCode) && $vnp_BankCode != "") {
            $inputData['vnp_BankCode'] = $vnp_BankCode;
        }

        ksort($inputData);
        $query = "";
        $i = 0;
        $hashdata = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        $vnp_Url = $vnp_Url . "?" . $query;
        if (isset($vnp_HashSecret)) {
            $vnpSecureHash =   hash_hmac('sha512', $hashdata, $vnp_HashSecret);
            $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
        }
        
        return redirect($vnp_Url);
    }

    /**
     * Xử lý kết quả trả về từ VNPAY.
     */
    public function vnpayCallback(Request $request)
    {
        $vnp_HashSecret = env('VNP_HASHSECRET');
        $inputData = $request->all();
        $vnp_SecureHash = $inputData['vnp_SecureHash'];
        unset($inputData['vnp_SecureHash']);
        
        ksort($inputData);
        $hashData = "";
        foreach ($inputData as $key => $value) {
            $hashData .= urlencode($key) . '=' . urlencode($value) . '&';
        }
        $hashData = rtrim($hashData, '&');

        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);

        if ($secureHash == $vnp_SecureHash) {
            if ($inputData['vnp_ResponseCode'] == '00') {
                // Giao dịch thành công
                $orderCode = $inputData['vnp_TxnRef'];
                $order = Order::where('order_code', $orderCode)->first();
                if ($order) {
                    $order->status = 'processing';
                    $order->save();
                    Cart::where('user_id', $order->user_id)->delete();

                    // Gửi email xác nhận
                    Mail::to($order->user->email)->send(new OrderPlaced($order));
                }
                return redirect()->route('orders.show', $order)->with('success', 'Thanh toán thành công! Cảm ơn bạn đã mua hàng.');
            }
        }
        
        return redirect()->route('cart.index')->with('error', 'Thanh toán không thành công hoặc đã có lỗi xảy ra.');
    }
}
