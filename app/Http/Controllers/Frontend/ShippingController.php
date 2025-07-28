<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ShippingController extends Controller
{
    /**
     * Tính phí vận chuyển từ GHTK.
     */
    public function getFee(Request $request)
    {
        $request->validate([
            'province' => 'required|string',
            'district' => 'required|string',
            'ward'     => 'required|string',
            'address'  => 'required|string',
        ]);

        $cartItems = Cart::where('user_id', Auth::id())->with('variant')->get();
        if ($cartItems->isEmpty()) {
            return response()->json(['error' => 'Giỏ hàng trống'], 400);
        }

        // Giả định tổng khối lượng (đơn vị: gram)
        $total_weight = $cartItems->sum(function ($item) {
            // Giả định mỗi variant có trường 'weight', nếu không có thì mặc định 200g
            $weight = $item->variant->weight ?? 200;
            return $item->quantity * $weight;
        });
        // Đảm bảo trọng lượng tối thiểu là 1g
        if ($total_weight == 0) {
            $total_weight = 1;
        }

        // Lấy tổng giá trị đơn hàng
        $total_value = 0;
        foreach ($cartItems as $item) {
            $price = $item->variant->sale_price > 0 ? $item->variant->sale_price : $item->variant->price;
            $total_value += $price * $item->quantity;
        }

        // Thông tin kho hàng của bạn (nên được lưu trong config hoặc DB)
        $pick_province = "Thành phố Đà Nẵng";
        $pick_district = "Quận Liên Chiểu";

        try {
            $response = Http::withHeaders([
                'Token' => env('GHTK_TOKEN'),
            ])->get(env('GHTK_API_URL') . '/services/shipment/fee', [
                'pick_province' => $pick_province,
                'pick_district' => $pick_district,
                'province'      => $request->province,
                'district'      => $request->district,
                'ward'          => $request->ward,
                'address'       => $request->address,
                'weight'        => $total_weight,
                'value'         => $total_value,
            ]);

            Log::info('GHTK Fee Request:', $request->all());
            Log::info('GHTK Fee Response:', ['status' => $response->status(), 'body' => $response->json()]);

            if (!$response->successful()) {
                return response()->json(['error' => 'Lỗi kết nối đến GHTK.', 'details' => $response->body()], 500);
            }

            $data = $response->json();

            if (isset($data['success']) && $data['success'] === true && isset($data['fee'])) {
                // GHTK thường trả về một gói cước duy nhất
                return response()->json([
                    'name' => $data['fee']['name'],
                    'fee'  => $data['fee']['fee'],
                ]);
            } else {
                return response()->json(['error' => $data['message'] ?? 'Không thể tính phí vận chuyển.'], 400);
            }

        } catch (\Exception $e) {
            Log::error('GHTK Shipping Fee Exception: ' . $e->getMessage());
            return response()->json(['error' => 'Lỗi hệ thống: ' . $e->getMessage()], 500);
        }
    }
}
