<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GhtkApiService
{
    protected $token;
    protected $apiUrl;
    protected $pickProvince;
    protected $pickDistrict;
    protected $pickWard;
    protected $pickAddress;
    protected $pickName;
    protected $pickTel;

    public function __construct()
    {
        $this->token = env('GHTK_TOKEN');
        $this->apiUrl = env('GHTK_API_URL');
        
        // Cấu hình thông tin kho hàng của bạn trong file .env
        $this->pickProvince = env('GHTK_PICK_PROVINCE', "Thành phố Đà Nẵng");
        $this->pickDistrict = env('GHTK_PICK_DISTRICT', "Quận Liên Chiểu");
        $this->pickWard = env('GHTK_PICK_WARD', "Phường Hòa Khánh Bắc"); // Cần thêm phường/xã kho hàng
        $this->pickAddress = env('GHTK_PICK_ADDRESS', "123 Âu Cơ"); // Địa chỉ cụ thể của kho
        $this->pickName = env('GHTK_PICK_NAME', "My Shop");
        $this->pickTel = env('GHTK_PICK_TEL', "0905123456");
    }

    /**
     * Gửi yêu cầu HTTP đến GHTK API.
     */
    private function callApi(string $endpoint, array $data = [], string $method = 'GET')
    {
        try {
            $url = rtrim($this->apiUrl, '/') . '/' . ltrim($endpoint, '/');
            $headers = [
                'Token' => $this->token,
                'Content-Type' => 'application/json',
            ];

            if ($method === 'POST') {
                $response = Http::withHeaders($headers)->post($url, $data);
            } else {
                $response = Http::withHeaders($headers)->get($url, $data);
            }
            
            Log::info("GHTK API Request ({$method} {$endpoint}): ", $data);
            Log::info("GHTK API Response ({$endpoint}): ", ['status' => $response->status(), 'body' => $response->json()]);

            return $response->json();

        } catch (\Exception $e) {
            Log::error("GHTK API Exception ({$endpoint}): " . $e->getMessage());
            return ['success' => false, 'message' => 'Lỗi kết nối đến GHTK: ' . $e->getMessage()];
        }
    }

    /**
     * Tạo đơn hàng trên hệ thống GHTK.
     */
    public function createOrder(array $orderData, array $products)
    {
        $payload = [
            "products" => $products,
            "order"    => [
                "id"           => $orderData['order_code'],
                "pick_name"    => $this->pickName,
                "pick_address" => $this->pickAddress,
                "pick_province"=> $this->pickProvince,
                "pick_district"=> $this->pickDistrict,
                "pick_ward"    => $this->pickWard,
                "pick_tel"     => $this->pickTel,
                "name"         => $orderData['receiver_name'],
                "address"      => $orderData['address'],
                "province"     => $orderData['city'],
                "district"     => $orderData['district'],
                "ward"         => $orderData['ward'],
                "hamlet"       => "Khác",
                "tel"          => $orderData['phone'],
                "note"         => $orderData['note'],
                "email"        => $orderData['user_email'],
                "is_freeship"  => "0", // 1: free ship, 0: không free ship
                "pick_money"   => $orderData['payment_method'] === 'cod' ? $orderData['final_amount'] : 0, // Tiền thu hộ
                "value"        => $orderData['total_amount'], // Giá trị đơn hàng (để tính bảo hiểm)
                "transport"    => "road", // 'road' hoặc 'fly'
            ]
        ];

        return $this->callApi('services/shipment/order', $payload, 'POST');
    }

    /**
     * Hủy đơn hàng trên hệ thống GHTK.
     */
    public function cancelOrder(string $labelId)
    {
        return $this->callApi("services/shipment/cancel/{$labelId}", [], 'POST');
    }
}
