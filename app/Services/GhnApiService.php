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
        $this->token  = config('services.ghn.token');
        $this->shopId = config('services.ghn.shop_id');
    }

    /**
     * Hàm chung để thực hiện request, luôn gửi kèm header xác thực.
     */
    protected function makeRequest($method, $endpoint, $data = [])
    {
        $headers = [
            'Token'  => $this->token,
            'ShopId' => (int) $this->shopId, // GHN yêu cầu ShopId là số nguyên
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

    /**
     * LẤY DỊCH VỤ KHẢ DỤNG – BỔ SUNG to_ward_code và from_ward
     */
    public function getAvailableServices($to_district_id, $to_ward_code = null)
    {
        $payload = [
            'shop_id'       => (int) $this->shopId,
            'from_district' => (int) config('services.ghn.pick_district_id'),
            'to_district'   => (int) $to_district_id,
        ];

        // Truyền ward để GHN xác định đúng tuyến
        if ($to_ward_code) {
            $payload['to_ward_code'] = (string) $to_ward_code;
        }
        $fromWard = config('services.ghn.pick_ward_code');
        if ($fromWard) {
            // Theo tài liệu GHN: available-services nhận key "from_ward"
            $payload['from_ward'] = (string) $fromWard;
        }

        $response = $this->makeRequest('post', '/v2/shipping-order/available-services', $payload);
        return $response->json()['data'] ?? [];
    }

    /**
     * TÍNH PHÍ – BỔ SUNG from_ward_code + bỏ qua dịch vụ "Hàng nặng" khi không đạt ngưỡng
     * + fallback thử theo service_type_id nếu service_id báo "route not found".
     */
    public function getShippingFee(array $params)
    {
        $services = $this->getAvailableServices($params['to_district_id'], $params['to_ward_code']);
        Log::info('GHN Available Services:', ['services' => $services]);

        if (empty($services)) {
            return ['success' => false, 'message' => 'Không có dịch vụ vận chuyển đến khu vực này (available-services trả rỗng).'];
        }

        $minHeavyWeight = (int) (config('services.ghn.min_heavy_weight') ?? 15000); // 15.000g mặc định
        $fromWard       = config('services.ghn.pick_ward_code');

        foreach ($services as $service) {
            // BỎ QUA dịch vụ "Hàng nặng" (type=5) nếu cân nặng chưa đạt ngưỡng
            if (($service['service_type_id'] ?? null) == 5 && (int) $params['weight'] < $minHeavyWeight) {
                continue;
            }

            $payload = [
                'service_id'       => $service['service_id'],
                'insurance_value'  => (int) $params['value'],
                'to_ward_code'     => (string) $params['to_ward_code'],
                'to_district_id'   => (int) $params['to_district_id'],
                'from_district_id' => (int) config('services.ghn.pick_district_id'),
                'weight'           => (int) $params['weight'],
                'length'           => 20,
                'width'            => 20,
                'height'           => 10,
            ];
            if ($fromWard) {
                // Fee yêu cầu "from_ward_code"
                $payload['from_ward_code'] = (string) $fromWard;
            }

            $response = $this->makeRequest('post', '/v2/shipping-order/fee', $payload);

            if ($response->successful() && ($response->json()['code'] ?? 0) === 200) {
                $feeData         = $response->json()['data'];
                $feeData['name'] = $service['short_name'] ?? 'Giao hàng';
                return ['success' => true, 'data' => $feeData];
            }

            // Nếu route not found → thử lại theo service_type_id (một số tenant GHN map tuyến theo type)
            $body = $response->json();
            $msg  = strtolower($body['message'] ?? '');
            if (str_contains($msg, 'route not found')) {
                $payload2 = $payload;
                unset($payload2['service_id']);
                $payload2['service_type_id'] = $service['service_type_id'] ?? null;

                $response2 = $this->makeRequest('post', '/v2/shipping-order/fee', $payload2);

                if ($response2->successful() && ($response2->json()['code'] ?? 0) === 200) {
                    $feeData         = $response2->json()['data'];
                    $feeData['name'] = $service['short_name'] ?? 'Giao hàng';
                    return ['success' => true, 'data' => $feeData];
                }

                Log::warning('GHN fee failed (fallback by service_type_id)', [
                    'service' => $service,
                    'payload' => $payload2,
                    'status'  => $response2->status(),
                    'body'    => $response2->json(),
                ]);
            } else {
                Log::warning('GHN fee failed', [
                    'service' => $service,
                    'payload' => $payload,
                    'status'  => $response->status(),
                    'body'    => $body,
                ]);
            }
        }

        return ['success' => false, 'message' => 'Đã thử tất cả dịch vụ nhưng GHN vẫn từ chối tính phí (route/weight). Vui lòng kiểm tra pick_district_id/ward và cân nặng.'];
    }

    public function createOrder(array $params)
    {
        // Giữ nguyên, nhưng thêm ward khi tạo đơn (đã có from_ward_code)
        $services = $this->getAvailableServices($params['to_district_id'], $params['to_ward_code'] ?? null);
        if (empty($services)) {
            return ['success' => false, 'message' => 'Không có dịch vụ vận chuyển khả dụng cho khu vực này.'];
        }

        $service_id = $services[0]['service_id'];

        $payload = array_merge($params, [
            'service_id'       => $service_id,
            'payment_type_id'  => 2, // 2: Người nhận trả phí
            'required_note'    => 'CHOXEMHANGKHONGTHU',
            'from_name'        => config('services.ghn.pick_name'),
            'from_phone'       => config('services.ghn.pick_tel'),
            'from_address'     => config('services.ghn.pick_address'),
            'from_ward_code'   => config('services.ghn.pick_ward_code'),
            'from_district_id' => (int) config('services.ghn.pick_district_id'),
            'weight'           => (int) $params['weight'],
            'length'           => 20,
            'width'            => 20,
            'height'           => 10,
        ]);

        $response = $this->makeRequest('post', '/v2/shipping-order/create', $payload);
        if ($response->successful() && ($response->json()['code'] ?? 0) === 200) {
            return ['success' => true, 'order' => $response->json()['data']];
        }
        return ['success' => false, 'message' => $response->json()['message'] ?? 'Lỗi tạo đơn GHN.'];
    }

    public function cancelOrder(string $trackingCode)
    {
        $response = $this->makeRequest('post', '/v2/switch-status/cancel', [
            'order_codes' => [$trackingCode],
        ]);
        if ($response->successful() && ($response->json()['code'] ?? 0) === 200) {
            return ['success' => true, 'data' => $response->json()['data']];
        }
        return ['success' => false, 'message' => $response->json()['message'] ?? 'Lỗi hủy đơn GHN.'];
    }
}
