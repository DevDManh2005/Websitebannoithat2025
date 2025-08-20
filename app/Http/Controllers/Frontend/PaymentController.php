<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Mail\OrderPlaced;
use App\Models\Cart;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;   // <-- thêm
use Illuminate\Support\Facades\Log;  // <-- thêm
use Illuminate\Support\Facades\Mail;

class PaymentController extends Controller
{
    /**
     * Tạo URL thanh toán VNPAY và chuyển hướng người dùng.
     */
    public function createVnpayPayment(Order $order)
    {
        // Cho phép: chủ đơn hoặc admin/staff
        $u = auth()->user();
        $isOwner = $u && ((int)$u->id === (int)$order->user_id);
        $role    = optional($u->role)->name;
        $isStaff = in_array($role, ['admin','staff'], true);
        abort_unless($isOwner || $isStaff, 403);

        $vnp_TmnCode    = (string) env('VNP_TMNCODE');
        $vnp_HashSecret = (string) env('VNP_HASHSECRET');
        $vnp_Url        = (string) env('VNP_URL', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html');

        // IP: ép IPv4
        $ip = request()->header('CF-Connecting-IP')
            ?? (request()->header('X-Forwarded-For') ? explode(',', request()->header('X-Forwarded-For'))[0] : null)
            ?? request()->ip();
        if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            $ip = '127.0.0.1';
        }

        // ReturnUrl tuyệt đối (https)
        $vnp_ReturnUrl = route('payment.vnpay.return', [], true);

        // TxnRef: chỉ chữ–số
        $txnRef = (string) ($order->order_code ?? $order->id);


        // Tính amount an toàn
        $baseAmount = $order->final_amount
            ?? $order->total_amount
            ?? (float) $order->items()->sum(DB::raw('price * quantity'));
        $amountVnd = (int) round($baseAmount);

        if ($amountVnd <= 0) {
            Log::warning('VNPAY amount invalid', ['order' => $txnRef, 'baseAmount' => $baseAmount]);
            return redirect()->route('orders.show', $order)->with('error', 'Số tiền thanh toán không hợp lệ.');
        }

        $input = [
            'vnp_Version'    => '2.1.0',
            'vnp_TmnCode'    => $vnp_TmnCode,
            'vnp_Amount'     => $amountVnd * 100, // VND x100
            'vnp_Command'    => 'pay',
            'vnp_CreateDate' => now('Asia/Ho_Chi_Minh')->format('YmdHis'),
            // 'vnp_ExpireDate' => now('Asia/Ho_Chi_Minh')->addMinutes(15)->format('YmdHis'), // thêm lại khi cần
            'vnp_CurrCode'   => 'VND',
            'vnp_IpAddr'     => $ip,
            'vnp_Locale'     => 'vn',
            'vnp_OrderInfo'  => 'Thanh toan don hang '.$txnRef,
            'vnp_OrderType'  => 'other',
            'vnp_ReturnUrl'  => $vnp_ReturnUrl,
            'vnp_TxnRef'     => $txnRef,
        ];

        // Debug: không gửi BankCode; khi ổn có thể set NCB qua .env
        $bank = trim((string) env('VNP_BANKCODE', ''));
        if ($bank !== '') {
            $input['vnp_BankCode'] = $bank;
        }

        // Ký & build query theo spec VNPAY
        [$hashData, $query] = $this->buildSignedQuery($input);
        $vnp_SecureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);
        $payUrl = rtrim($vnp_Url, '?') . '?' . $query . 'vnp_SecureHash=' . $vnp_SecureHash;

        Log::info('VNPAY Create Payment URL', [
            'order'        => $txnRef,
            'amount_vnd'   => $amountVnd,
            'ip'           => $ip,
            'return'       => $vnp_ReturnUrl,
            'signed_query' => $query,
            'url'          => $payUrl,
        ]);

        return redirect()->away($payUrl);
    }

    /**
     * IPN: VNPAY gọi server-to-server (GET/POST). Nguồn sự thật của trạng thái.
     */
    public function vnpayIpn(Request $request)
    {
        $data = $request->all();
        Log::info('VNPAY IPN IN', $data);

        $secret     = (string) env('VNP_HASHSECRET');
        $secureHash = (string) ($data['vnp_SecureHash'] ?? '');
        unset($data['vnp_SecureHash'], $data['vnp_SecureHashType']);

        $signed = [];
        foreach ($data as $k => $v) {
            if (strpos($k, 'vnp_') === 0) $signed[$k] = $v;
        }
        [$hashData] = $this->buildSignedQuery($signed);
        $calc = hash_hmac('sha512', $hashData, $secret);

        if (!hash_equals($calc, $secureHash)) {
            Log::warning('VNPAY IPN INVALID SIGNATURE', [
                'signed_query' => $hashData,
                'calc'         => $calc,
                'recv'         => $secureHash,
            ]);
            return response()->json(['RspCode' => '97', 'Message' => 'Invalid signature']);
        }

        $orderCode = (string) ($signed['vnp_TxnRef'] ?? '');
        $order     = Order::where('order_code', $orderCode)->first();

        if (!$order) {
            Log::warning('VNPAY IPN ORDER NOT FOUND', ['order_code' => $orderCode]);
            return response()->json(['RspCode' => '01', 'Message' => 'Order not found']);
        }

        // Số tiền phải khớp
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
            if ((int) $order->is_paid !== 1) { $order->is_paid = 1; $changed = true; }
            if ($order->payment_status !== 'paid') { $order->payment_status = 'paid'; $changed = true; }
            if ($order->payment_method !== 'vnpay') { $order->payment_method = 'vnpay'; $changed = true; }
            if (is_null($order->paid_at)) { $order->paid_at = now(); $changed = true; }
            if (empty($order->payment_ref)) { $order->payment_ref = $signed['vnp_TransactionNo'] ?? null; $changed = true; }
            if ($order->status === 'pending') { $order->status = 'processing'; $changed = true; }

            if ($changed) {
                $order->save();
                try { Cart::where('user_id', $order->user_id)->delete(); } catch (\Throwable) {}
                try { Mail::to($order->user->email)->send(new OrderPlaced($order)); } catch (\Throwable) {}
            }

            Log::info('VNPAY IPN OK', ['order' => $order->order_code]);
            return response()->json(['RspCode' => '00', 'Message' => 'Confirm Success']);
        }

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
     * Return URL: cập nhật mềm để user thấy ngay; IPN vẫn là nguồn sự thật.
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

        $secret     = (string) env('VNP_HASHSECRET');
        $secureHash = (string) ($data['vnp_SecureHash'] ?? '');
        unset($data['vnp_SecureHash'], $data['vnp_SecureHashType']);

        $signed = [];
        foreach ($data as $k => $v) {
            if (strpos($k, 'vnp_') === 0) $signed[$k] = $v;
        }
        [$hashData] = $this->buildSignedQuery($signed);
        $calc = hash_hmac('sha512', $hashData, $secret);

        if (hash_equals($calc, $secureHash) && ($signed['vnp_ResponseCode'] ?? '') === '00') {
            $changed = false;
            if ((int) $order->is_paid !== 1) { $order->is_paid = 1; $changed = true; }
            if ($order->payment_status !== 'paid') { $order->payment_status = 'paid'; $changed = true; }
            if ($order->payment_method !== 'vnpay') { $order->payment_method = 'vnpay'; $changed = true; }
            if (is_null($order->paid_at)) { $order->paid_at = now(); $changed = true; }
            if (empty($order->payment_ref)) { $order->payment_ref = $signed['vnp_TransactionNo'] ?? null; $changed = true; }
            if ($order->status === 'pending') { $order->status = 'processing'; $changed = true; }

            if ($changed) {
                $order->save();
                try { Cart::where('user_id', $order->user_id)->delete(); } catch (\Throwable) {}
                try { Mail::to($order->user->email)->send(new OrderPlaced($order)); } catch (\Throwable) {}
            }

            return redirect()->route('orders.show', $order)->with('success', 'Giao dịch đã tiếp nhận.');
        }

        return redirect()->route('orders.show', $order)->with('error', 'Thanh toán không thành công hoặc bị huỷ.');
    }

    /**
     * Helper: sort + build chuỗi ký và query string ổn định (urlencode key & value).
     * Trả về: [$hashData, $queryString]
     */
    private function buildSignedQuery(array $params): array
    {
        ksort($params);

        $hashData = '';
        $query    = '';
        $i        = 0;

        foreach ($params as $key => $value) {
            $encKey = urlencode((string) $key);
            $encVal = urlencode((string) $value);

            if ($i === 1) {
                $hashData .= '&' . $encKey . '=' . $encVal;
            } else {
                $hashData .= $encKey . '=' . $encVal;
                $i = 1;
            }

            $query .= $encKey . '=' . $encVal . '&';
        }

        return [$hashData, $query];
    }
}
