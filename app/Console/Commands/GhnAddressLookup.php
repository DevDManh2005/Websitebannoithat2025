<?php

namespace App\Console\Commands;

use App\Services\GhnApiService;
use Illuminate\Console\Command;

class GhnAddressLookup extends Command
{
    protected $signature = 'ghn:lookup-address';
    protected $description = 'Tra cứu ID và Mã địa chỉ từ Giao Hàng Nhanh API';

    public function handle(GhnApiService $ghn)
    {
        $this->info("Bắt đầu tra cứu địa chỉ GHN...");

        // Lấy danh sách tỉnh thành
        $provinces = $ghn->getProvinces();
        $provinceNames = collect($provinces)->pluck('ProvinceName', 'ProvinceID')->toArray();
        $selectedProvinceName = $this->choice('Vui lòng chọn Tỉnh/Thành phố:', $provinceNames);
        $provinceId = array_search($selectedProvinceName, $provinceNames);
        $this->line("=> Tỉnh/Thành: <info>{$selectedProvinceName}</info> (ID: <info>{$provinceId}</info>)");

        // Lấy danh sách quận huyện
        $districts = $ghn->getDistricts($provinceId);
        $districtNames = collect($districts)->pluck('DistrictName', 'DistrictID')->toArray();
        $selectedDistrictName = $this->choice('Vui lòng chọn Quận/Huyện:', $districtNames);
        $districtId = array_search($selectedDistrictName, $districtNames);
        $this->line("=> Quận/Huyện: <info>{$selectedDistrictName}</info> (ID: <info>{$districtId}</info>)");

        // Lấy danh sách phường xã
        $wards = $ghn->getWards($districtId);
        $wardNames = collect($wards)->pluck('WardName', 'WardCode')->toArray();
        $selectedWardName = $this->choice('Vui lòng chọn Phường/Xã:', $wardNames);
        $wardCode = array_search($selectedWardName, $wardNames);
        $this->line("=> Phường/Xã: <info>{$selectedWardName}</info> (Code: <info>{$wardCode}</info>)");
        
        $this->info("\nHoàn tất! Dưới đây là thông tin bạn cần cho file .env:");
        $this->line("GHN_PICK_DISTRICT_ID={$districtId}");
        $this->line("GHN_PICK_WARD_CODE={$wardCode}");

        return 0;
    }
}