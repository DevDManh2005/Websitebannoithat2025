<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderPlaced;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    /**
     * Tạo URL thanh toán VNPAY và chuyển hướng người dùng.
     * Yêu cầu APP_URL là https khi test (ngrok).
     */
    public function createVnpayPayment(Order $order)
    {
        // Chỉ cho chủ đơn tạo thanh toán
        abort_unless(auth()->id() === $order->user_id, 403);

        $vnp_TmnCode    = env('VNP_TMNCODE');
        $vnp_HashSecret = env('VNP_HASHSECRET');
        $vnp_Url        = env('VNP_URL', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html');

        // Lấy IP thật (qua proxy/CDN nếu có)
        $ip = request()->header('CF-Connecting-IP')
            ?? explode(',', (string)request()->header('X-Forwarded-For'))[0]
            ?? request()->ip();

        // ReturnUrl: ưu tiên .env nếu có, fallback route()
        $vnp_ReturnUrl = env('VNP_RETURNURL') ?: route('payment.vnpay.return');

        // TxnRef chỉ chữ-số cho an toàn
        $txnRef = preg_replace('/[^A-Za-z0-9]/', '', (string)($order->order_code ?? $order->id));

        $input = [
            'vnp_Version'    => '2.1.0',
            'vnp_TmnCode'    => $vnp_TmnCode,
            'vnp_Amount'     => (int) round((float)$order->final_amount) * 100, // NHÂN 100
            'vnp_Command'    => 'pay',
            'vnp_CreateDate' => now('Asia/Ho_Chi_Minh')->format('YmdHis'),
            'vnp_ExpireDate' => now('Asia/Ho_Chi_Minh')->addMinutes(15)->format('YmdHis'),
            'vnp_CurrCode'   => 'VND',
            'vnp_IpAddr'     => $ip,
            'vnp_Locale'     => 'vn',
            'vnp_OrderInfo'  => 'Thanh toan don hang ' . $txnRef, // ASCII
            'vnp_OrderType'  => 'other',
            'vnp_ReturnUrl'  => $vnp_ReturnUrl,
            'vnp_TxnRef'     => $txnRef,
        ];

        // SANDBOX: để test nhanh, set NCB (có thể bỏ trống nếu muốn chọn trên cổng)
        $bank = trim((string) env('VNP_BANKCODE', 'NCB'));
        if ($bank !== '') {
            $input['vnp_BankCode'] = $bank;
        }

        ksort($input);

        // Theo mẫu VNPAY: urlencode key & value khi ký
        $hashdata = '';
        $query    = '';
        $i = 0;
        foreach ($input as $key => $value) {
            $encKey = urlencode($key);
            $encVal = urlencode($value);
            if ($i == 1) {
                $hashdata .= '&' . $encKey . '=' . $encVal;
            } else {
                $hashdata .= $encKey . '=' . $encVal;
                $i = 1;
            }
            $query .= $encKey . '=' . $encVal . '&';
        }

        $vnp_SecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
        $payUrl = rtrim($vnp_Url, '?') . '?' . $query . 'vnp_SecureHash=' . $vnp_SecureHash;

        Log::info('VNPAY Create Payment URL', [
            'order'        => $txnRef,
            'signed_query' => $query,
            'url'          => $payUrl,
        ]);


        return redirect()->away($payUrl);
    }


    /**
     * IPN: VNPAY gọi server-to-server (POST). Trạng thái cuối cùng dựa vào đây.
     * Route phải public & bypass CSRF.
     */
    public function vnpayIpn(Request $request)
    {
        $data = $request->all();
        Log::info('VNPAY IPN IN', $data);

        $secret     = env('VNP_HASHSECRET');
        $secureHash = $data['vnp_SecureHash'] ?? '';

        unset($data['vnp_SecureHash'], $data['vnp_SecureHashType']);

        $signed = [];
        foreach ($data as $k => $v) {
            if (strpos($k, 'vnp_') === 0) {
                $signed[$k] = $v;
            }
        }
        ksort($signed);

        $signedQuery = http_build_query($signed);
        $calc        = hash_hmac('sha512', $signedQuery, $secret);

        if (!hash_equals($calc, $secureHash)) {
            Log::warning('VNPAY IPN INVALID SIGNATURE', [
                'signed_query' => $signedQuery,
                'calc'         => $calc,
                'recv'         => $secureHash,
            ]);
            return response()->json(['RspCode' => '97', 'Message' => 'Invalid signature']);
        }

        $orderCode = $signed['vnp_TxnRef'] ?? '';
        $order = Order::where('order_code', $orderCode)->first();

        if (!$order) {
            Log::warning('VNPAY IPN ORDER NOT FOUND', ['order_code' => $orderCode]);
            return response()->json(['RspCode' => '01', 'Message' => 'Order not found']);
        }

        // Kiểm tra số tiền
        $expected = (int) $order->final_amount * 100;
        $amount   = (int) ($signed['vnp_Amount'] ?? 0);
        if ($amount !== $expected) {
            Log::warning('VNPAY IPN INVALID AMOUNT', ['expected' => $expected, 'amount' => $amount]);
            return response()->json(['RspCode' => '04', 'Message' => 'Invalid amount']);
        }

        $isOk = ($signed['vnp_ResponseCode'] ?? '') === '00'
            && ($signed['vnp_TransactionStatus'] ?? '') === '00';

        if ($isOk) {
            $changed = false;

            if ((int) $order->is_paid !== 1) {
                $order->is_paid = 1;
                $changed = true;
            }
            if ($order->payment_status !== 'paid') {
                $order->payment_status = 'paid';
                $changed = true;
            }
            if ($order->payment_method !== 'vnpay') {
                $order->payment_method = 'vnpay';
                $changed = true;
            }
            if (is_null($order->paid_at)) {
                $order->paid_at = now();
                $changed = true;
            }
            if (empty($order->payment_ref)) {
                $order->payment_ref = $signed['vnp_TransactionNo'] ?? null;
                $changed = true;
            }
            if ($order->status === 'pending') {
                $order->status = 'processing';
                $changed = true;
            }

            if ($changed) {
                $order->save();
                try {
                    Cart::where('user_id', $order->user_id)->delete();
                } catch (\Throwable $e) {
                }
                try {
                    Mail::to($order->user->email)->send(new OrderPlaced($order));
                } catch (\Throwable $e) {
                }
            }

            Log::info('VNPAY IPN OK', ['order' => $order->order_code]);
            return response()->json(['RspCode' => '00', 'Message' => 'Confirm Success']);
        }

        // Thất bại
        if ($order->payment_status !== 'failed') {
            $order->payment_status = 'failed';
            $order->save();
        }
        Log::info('VNPAY IPN FAILED', [
            'order' => $order->order_code,
            'code'  => $signed['vnp_ResponseCode'] ?? null,
        ]);
        return response()->json(['RspCode' => '00', 'Message' => 'Payment Failed']);
    }

    /**
     * Return URL (GET): người dùng quay về sau khi thanh toán.
     * Có thể cập nhật mềm trạng thái để user thấy ngay, nhưng nguồn sự thật vẫn là IPN.
     */
    public function vnpayReturn(Request $request)
    {
        $data = $request->all();
        Log::info('VNPAY RETURN IN', $data);

        $order = !empty($data['vnp_TxnRef'])
            ? Order::where('order_code', $data['vnp_TxnRef'])->first()
            : null;

        if (!$order) {
            return redirect()->route('cart.index')->with('error', 'Không tìm thấy đơn hàng.');
        }

        // Xác minh chữ ký
        $secret     = env('VNP_HASHSECRET');
        $secureHash = $data['vnp_SecureHash'] ?? '';
        unset($data['vnp_SecureHash'], $data['vnp_SecureHashType']);

        $signed = [];
        foreach ($data as $k => $v) {
            if (strpos($k, 'vnp_') === 0) $signed[$k] = $v;
        }
        ksort($signed);

        $signedQuery = http_build_query($signed);
        $calc        = hash_hmac('sha512', $signedQuery, $secret);

        if (hash_equals($calc, $secureHash) && ($signed['vnp_ResponseCode'] ?? '') === '00') {
            // Cập nhật “mềm” để user thấy ngay (IPN vẫn là nguồn sự thật)
            $changed = false;
            if ((int) $order->is_paid !== 1) {
                $order->is_paid = 1;
                $changed = true;
            }
            if ($order->payment_status !== 'paid') {
                $order->payment_status = 'paid';
                $changed = true;
            }
            if ($order->payment_method !== 'vnpay') {
                $order->payment_method = 'vnpay';
                $changed = true;
            }
            if (is_null($order->paid_at)) {
                $order->paid_at = now();
                $changed = true;
            }
            if (empty($order->payment_ref)) {
                $order->payment_ref = $signed['vnp_TransactionNo'] ?? null;
                $changed = true;
            }
            if ($order->status === 'pending') {
                $order->status = 'processing';
                $changed = true;
            }

            if ($changed) {
                $order->save();
                try {
                    Cart::where('user_id', $order->user_id)->delete();
                } catch (\Throwable $e) {
                }
                try {
                    Mail::to($order->user->email)->send(new OrderPlaced($order));
                } catch (\Throwable $e) {
                }
            }

            return redirect()
                ->route('orders.show', $order)
                ->with('success', 'Giao dịch đã tiếp nhận.');
        }

        return redirect()
            ->route('orders.show', $order)
            ->with('error', 'Thanh toán không thành công hoặc bị huỷ.');
    }
}
