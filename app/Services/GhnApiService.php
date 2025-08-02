<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GhnApiService
{
    protected $apiUrl;
    protected $token;
    protected $shopId;

    public function __construct()
    {
        $this->apiUrl = config('services.ghn.api_url');
        $this->token = config('services.ghn.token');
        $this->shopId = config('services.ghn.shop_id');
    }

    /**
     * Hàm chung để thực hiện request, luôn gửi kèm header xác thực.
     */
    protected function makeRequest($method, $endpoint, $data = [])
    {
        $headers = [
            'Token' => $this->token,
            'ShopId' => (int)$this->shopId // GHN yêu cầu ShopId là số nguyên
        ];

        $response = Http::withHeaders($headers)->$method($this->apiUrl . $endpoint, $data);
        
        Log::info("GHN API Request to {$endpoint}:", ['payload' => $data]);
        Log::info("GHN API Response from {$endpoint}:", ['status' => $response->status(), 'body' => $response->json()]);

        return $response;
    }

    public function getProvinces()
    {
        $response = $this->makeRequest('get', '/master-data/province');
        return $response->json()['data'] ?? [];
    }

    public function getDistricts($provinceId)
    {
        $response = $this->makeRequest('get', '/master-data/district', ['province_id' => $provinceId]);
        return $response->json()['data'] ?? [];
    }

    public function getWards($districtId)
    {
        $response = $this->makeRequest('get', '/master-data/ward', ['district_id' => $districtId]);
        return $response->json()['data'] ?? [];
    }

    public function getAvailableServices($to_district_id)
    {
        $response = $this->makeRequest('post', '/v2/shipping-order/available-services', [
            'shop_id' => (int)$this->shopId,
            'from_district' => (int)config('services.ghn.pick_district_id'),
            'to_district' => (int)$to_district_id,
        ]);
        return $response->json()['data'] ?? [];
    }

    public function getShippingFee(array $params)
    {
        $services = $this->getAvailableServices($params['to_district_id']);
        if (empty($services)) {
            return ['success' => false, 'message' => 'Không có dịch vụ vận chuyển khả dụng.'];
        }
        $service_id = $services[0]['service_id'];

        $payload = [
            'service_id' => $service_id,
            'insurance_value' => (int)$params['value'],
            'to_ward_code' => $params['to_ward_code'],
            'to_district_id' => (int)$params['to_district_id'],
            'from_district_id' => (int)config('services.ghn.pick_district_id'),
            'weight' => (int)$params['weight'],
            'length' => 20,
            'width' => 20,
            'height' => 10,
        ];

        $response = $this->makeRequest('post', '/v2/shipping-order/fee', $payload);
        
        if ($response->successful() && $response->json()['code'] === 200) {
            return ['success' => true, 'data' => $response->json()['data']];
        }
        return ['success' => false, 'message' => $response->json()['message'] ?? 'Lỗi tính phí GHN.'];
    }

    public function createOrder(array $params)
    {
        $services = $this->getAvailableServices($params['to_district_id']);
        if (empty($services)) {
            return ['success' => false, 'message' => 'Không có dịch vụ vận chuyển khả dụng cho khu vực này.'];
        }
        $service_id = $services[0]['service_id'];

        $payload = array_merge($params, [
            'service_id' => $service_id,
            'payment_type_id' => 2, // 2: Người nhận trả phí
            'required_note' => 'CHOXEMHANGKHONGTHU',
            'from_name' => config('services.ghn.pick_name'),
            'from_phone' => config('services.ghn.pick_tel'),
            'from_address' => config('services.ghn.pick_address'),
            'from_ward_code' => config('services.ghn.pick_ward_code'),
            'from_district_id' => (int)config('services.ghn.pick_district_id'),
            'weight' => (int)$params['weight'],
            'length' => 20,
            'width' => 20,
            'height' => 10,
        ]);

        $response = $this->makeRequest('post', '/v2/shipping-order/create', $payload);
        if ($response->successful() && $response->json()['code'] === 200) {
            return ['success' => true, 'order' => $response->json()['data']];
        }
        return ['success' => false, 'message' => $response->json()['message'] ?? 'Lỗi tạo đơn GHN.'];
    }

    public function cancelOrder(string $trackingCode)
    {
        $response = $this->makeRequest('post', '/v2/switch-status/cancel', [
            'order_codes' => [$trackingCode]
        ]);
        if ($response->successful() && $response->json()['code'] === 200) {
            return ['success' => true, 'data' => $response->json()['data']];
        }
        return ['success' => false, 'message' => $response->json()['message'] ?? 'Lỗi hủy đơn GHN.'];
    }
}