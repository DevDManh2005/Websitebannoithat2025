<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Services\GhnApiService;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    protected $apiService;

    public function __construct(GhnApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    public function getProvinces()
    {
        return response()->json($this->apiService->getProvinces());
    }

    public function getDistricts(Request $request)
    {
        $provinceId = $request->query('province_id');
        return response()->json($this->apiService->getDistricts($provinceId));
    }

    public function getWards(Request $request)
    {
        $districtId = $request->query('district_id');
        return response()->json($this->apiService->getWards($districtId));
    }
}