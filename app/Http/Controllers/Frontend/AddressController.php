<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AddressController extends Controller
{
    // Sử dụng API công cộng của GHTK để lấy dữ liệu địa chỉ
    private $ghtkAddressApiUrl = 'https://khachhang.giaohangtietkiem.vn/khach-hang/services/list-dia-chi';

    /**
     * Lấy danh sách Tỉnh/Thành phố.
     */
    public function getProvinces()
    {
        try {
            $response = Http::get($this->ghtkAddressApiUrl);
            $data = $response->json();
            // Chỉ trả về mảng 'data' nếu request thành công và có dữ liệu
            if (isset($data['success']) && $data['success'] && is_array($data['data'])) {
                return response()->json($data['data']);
            }
        } catch (\Exception $e) {
            Log::error('GHTK Address API Error (Provinces): ' . $e->getMessage());
        }
        return response()->json([]); // Trả về mảng rỗng nếu có lỗi
    }

    /**
     * Lấy danh sách Quận/Huyện dựa vào ID của Tỉnh/Thành.
     */
    public function getDistricts(Request $request)
    {
        $request->validate(['province_id' => 'required|integer']);
        try {
            $response = Http::get($this->ghtkAddressApiUrl, ['parent_id' => $request->province_id]);
            $data = $response->json();
            if (isset($data['success']) && $data['success'] && is_array($data['data'])) {
                return response()->json($data['data']);
            }
        } catch (\Exception $e) {
            Log::error('GHTK Address API Error (Districts): ' . $e->getMessage());
        }
        return response()->json([]);
    }

    /**
     * Lấy danh sách Phường/Xã dựa vào ID của Quận/Huyện.
     */
    public function getWards(Request $request)
    {
        $request->validate(['district_id' => 'required|integer']);
        try {
            $response = Http::get($this->ghtkAddressApiUrl, ['parent_id' => $request->district_id]);
            $data = $response->json();
            if (isset($data['success']) && $data['success'] && is_array($data['data'])) {
                return response()->json($data['data']);
            }
        } catch (\Exception $e) {
            Log::error('GHTK Address API Error (Wards): ' . $e->getMessage());
        }
        return response()->json([]);
    }
}
