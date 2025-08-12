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

    // chỉ lấy item đã chọn
    $cartItems = Cart::where('user_id', Auth::id())
        ->where('is_selected', true)
        ->with('variant')
        ->get();

    if ($cartItems->isEmpty()) {
        return response()->json(['success' => false, 'message' => 'Không có sản phẩm được chọn.'], 400);
    }

    $total_weight = 0;
    $total_value  = 0;

    foreach ($cartItems as $item) {
        $itemWeight = (int)($item->variant->weight ?? 200);
        if ($itemWeight < 50) $itemWeight = 50;

        $price = $item->variant->sale_price > 0 ? $item->variant->sale_price : $item->variant->price;

        $total_weight += $itemWeight * $item->quantity;
        $total_value  += max(0, $price) * $item->quantity;
    }

    if ($total_weight <= 0) $total_weight = 200;

    $result = $this->apiService->getShippingFee([
        'to_district_id' => $data['to_district_id'],
        'to_ward_code'   => $data['to_ward_code'],
        'weight'         => $total_weight,
        'value'          => $total_value,
    ]);

    if ($result['success'] && !empty($result['data'])) {
        return response()->json([
            'success' => true,
            'data' => [
                'total' => $result['data']['total'],
                'name'  => $result['data']['name'] ?? 'Giao hàng',
            ]
        ]);
    }

    return response()->json([
        'success' => false,
        'message' => $result['message'] ?? 'Không thể tính phí vận chuyển cho địa chỉ này.'
    ], 400);
}

}
