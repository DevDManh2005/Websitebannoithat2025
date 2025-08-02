<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Services\GhnApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShippingController extends Controller
{
    protected $apiService;

    public function __construct(GhnApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    public function getFee(Request $request)
    {
        $data = $request->validate([
            'to_district_id' => 'required|integer',
            'to_ward_code'   => 'required|string',
        ]);

        $cartItems = Cart::where('user_id', Auth::id())->with('variant')->get();
        if ($cartItems->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Giỏ hàng trống'], 400);
        }

        $total_weight = 0;
        $total_value = 0;
        foreach ($cartItems as $item) {
            $total_weight += ($item->variant->weight ?? 200) * $item->quantity;
            $price = $item->variant->sale_price > 0 ? $item->variant->sale_price : $item->variant->price;
            $total_value += $price * $item->quantity;
        }

        $result = $this->apiService->getShippingFee([
            'to_district_id' => $data['to_district_id'],
            'to_ward_code'   => $data['to_ward_code'],
            'weight'         => $total_weight,
            'value'          => $total_value,
        ]);
        
        // Chuẩn hóa response trả về cho frontend
        if ($result['success'] && $result['data']) {
            return response()->json([
                'success' => true,
                'data' => [
                    'total' => $result['data']['total'],
                    'name'  => 'Giao hàng nhanh',
                ]
            ]);
        }
        
        return response()->json($result);
    }
}