<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Mail\OrderPlaced;
use App\Models\Cart;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class PaymentController extends Controller
{
    /**
     * Tạo URL thanh toán VNPAY và chuyển hướng người dùng.
     * - Cho phép: chủ đơn hoặc user có role 'admin' | 'staff'
     * - Ép IPv4 (VNPAY ưu tiên IPv4)
     * - ReturnUrl luôn tuyệt đối (https)
     * - TxnRef chỉ chữ–số, tối đa 32 ký tự
     */
    public function createVnpayPayment(Order $order)
    {
        // ---- Quyền truy cập: chủ đơn hoặc admin/staff
        $u       = auth()->user();
        $isOwner = $u && ((int) $u->id === (int) $order->user_id);
        $role    = optional($u->role)->name;
        $isStaff = in_array($role, ['admin', 'staff'], true);
        abort_unless($isOwner || $isStaff, 403);

        // ---- ENV (sandbox mặc định)
        $vnp_TmnCode    = env('VNP_TMNCODE');
        $vnp_HashSecret = env('VNP_HASHSECRET');
        $vnp_Url        = env('VNP_URL', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html');

        // ---- Lấy IPv4: qua CDN/proxy nếu có
        $ip = request()->header('CF-Connecting-IP')
            ?? (request()->header('X-Forwarded-For') ? explode(',', request()->header('X-Forwarded-For'))[0] : null)
            ?? request()->ip();
        $ip = trim((string) $ip);
        if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            $ip = '127.0.0.1';
        }

        // ---- Return URL tuyệt đối (https)
        $vnp_ReturnUrl = route('payment.vnpay.return', [], true);

        // ---- TxnRef chỉ chữ–số, tối đa 32 ký tự
        $txnRef = preg_replace('/[^A-Za-z0-9]/', '', (string) ($order->order_code ?? $order->id));
        $txnRef = substr($txnRef, 0, 32);

        // ---- Tham số bắt buộc
        $params = [
            'vnp_Version'    => '2.1.0',
            'vnp_TmnCode'    => $vnp_TmnCode,
            'vnp_Amount'     => (int) round((float) $order->final_amount) * 100, // NHÂN 100
            'vnp_Command'    => 'pay',
            'vnp_CreateDate' => now('Asia/Ho_Chi_Minh')->format('YmdHis'),
            // Khuyến nghị: test loại trừ ExpireDate để tránh lỗi 03, bật lại khi ổn định:
            // 'vnp_ExpireDate' => now('Asia/Ho_Chi_Minh')->addMinutes((int) env('VNP_EXPIRE_MIN', 0))->format('YmdHis'),
            'vnp_CurrCode'   => 'VND',
            'vnp_IpAddr'     => $ip,
            'vnp_Locale'     => 'vn',
            'vnp_OrderInfo'  => 'Thanh toan don hang ' . $txnRef, // ASCII
            'vnp_OrderType'  => 'other',
            'vnp_ReturnUrl'  => $vnp_ReturnUrl,
            'vnp_TxnRef'     => $txnRef,
        ];

        // ---- BankCode (để trống lúc debug lỗi 03; khi ổn có thể set NCB để test nhanh)
        $bank = trim((string) env('VNP_BANKCODE', ''));
        if ($bank !== '') {
            $params['vnp_BankCode'] = $bank;
        }

        // ---- Ký & tạo URL
        [$hashData, $query] = $this->buildSignedQuery($params);
        $vnp_SecureHash     = hash_hmac('sha512', $hashData, $vnp_HashSecret);
        $payUrl             = rtrim($vnp_Url, '?') . '?' . $query . 'vnp_SecureHash=' . $vnp_SecureHash;

        Log::info('VNPAY Create Payment URL', [
            'order'        => $txnRef,
            'ip'           => $ip,
            'return'       => $vnp_ReturnUrl,
            'signed_query' => $hashData,
            'url'          => $payUrl,
        ]);

        return redirect()->away($payUrl);
    }

    /**
     * IPN: VNPAY gọi server-to-server (GET/POST).
     * Nguồn sự thật của trạng thái thanh toán.
     */
    public function vnpayIpn(Request $request)
    {
        $data = $request->all();
        Log::info('VNPAY IPN IN', $data);

        $secret     = env('VNP_HASHSECRET');
        $secureHash = $data['vnp_SecureHash'] ?? '';
        unset($data['vnp_SecureHash'], $data['vnp_SecureHashType']);

        // Lọc đúng các tham số vnp_ và ký lại
        $signed = [];
        foreach ($data as $k => $v) {
            if (strpos($k, 'vnp_') === 0) {
                $signed[$k] = $v;
            }
        }
        [$hashData] = $this->buildSignedQuery($signed);
        $calc       = hash_hmac('sha512', $hashData, $secret);

        if (!hash_equals($calc, $secureHash)) {
            Log::warning('VNPAY IPN INVALID SIGNATURE', [
                'signed_query' => $hashData,
                'calc'         => $calc,
                'recv'         => $secureHash,
            ]);
            return response()->json(['RspCode' => '97', 'Message' => 'Invalid signature']);
        }

        $orderCode = $signed['vnp_TxnRef'] ?? '';
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
                try { Cart::where('user_id', $order->user_id)->delete(); } catch (\Throwable $e) {}
                try { Mail::to($order->user->email)->send(new OrderPlaced($order)); } catch (\Throwable $e) {}
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
     * Return URL (GET): người dùng quay về sau khi thanh toán.
     * Cập nhật mềm để user thấy ngay; IPN vẫn là nguồn sự thật.
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

        $secret     = env('VNP_HASHSECRET');
        $secureHash = $data['vnp_SecureHash'] ?? '';
        unset($data['vnp_SecureHash'], $data['vnp_SecureHashType']);

        $signed = [];
        foreach ($data as $k => $v) {
            if (strpos($k, 'vnp_') === 0) { $signed[$k] = $v; }
        }
        [$hashData] = $this->buildSignedQuery($signed);
        $calc       = hash_hmac('sha512', $hashData, $secret);

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
                try { Cart::where('user_id', $order->user_id)->delete(); } catch (\Throwable $e) {}
                try { Mail::to($order->user->email)->send(new OrderPlaced($order)); } catch (\Throwable $e) {}
            }

            return redirect()->route('orders.show', $order)->with('success', 'Giao dịch đã tiếp nhận.');
        }

        return redirect()->route('orders.show', $order)->with('error', 'Thanh toán không thành công hoặc bị huỷ.');
    }

    /**
     * Helper: sort + build chuỗi ký và query string theo cách ổn định.
     * Trả về: [$hashData, $queryString]
     */
    private function buildSignedQuery(array $params): array
    {
        ksort($params);

        $hashData = '';
        $query    = '';
        $i        = 0;

        foreach ($params as $key => $value) {
            // ký: urlencode key & value (ổn định với VNPAY v2.1)
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
