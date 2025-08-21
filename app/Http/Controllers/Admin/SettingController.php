<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::pluck('value', 'key')->all();
        return view('admins.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->except('_token', 'remove_image');

        // Upload ảnh
        foreach (['logo_light', 'logo_dark', 'favicon', 'og_image'] as $key) {
            if ($request->hasFile($key)) {
                $oldPath = Setting::where('key', $key)->value('value');
                if ($oldPath) Storage::disk('public')->delete($oldPath);
                $path = $request->file($key)->store('settings', 'public');
                $data[$key] = $path;
            }
        }

        // Xóa ảnh cũ
        if ($request->has('remove_image')) {
            foreach ((array) $request->input('remove_image') as $k) {
                $oldPath = Setting::where('key', $k)->value('value');
                if ($oldPath) Storage::disk('public')->delete($oldPath);
                $data[$k] = null;
            }
        }

        // Map input tiếng Việt -> ENV key
        $vnToEnv = [
            // SMTP
            'smtp_loai'        => 'MAIL_MAILER',
            'smtp_host'        => 'MAIL_HOST',
            'smtp_cong'        => 'MAIL_PORT',
            'smtp_tendangnhap' => 'MAIL_USERNAME',
            'smtp_matkhau'     => 'MAIL_PASSWORD',
            'smtp_mahoa'       => 'MAIL_ENCRYPTION',
            'smtp_tu_email'    => 'MAIL_FROM_ADDRESS',
            'smtp_tu_ten'      => 'MAIL_FROM_NAME',

            // VNPAY
            'vnp_duong_dan'          => 'VNP_URL',
            'vnp_api'                => 'VNP_API_URL',
            'vnp_ma_website'         => 'VNP_TMNCODE',
            'vnp_bi_mat'             => 'VNP_HASHSECRET',
            'vnp_return_url'         => 'VNP_RETURNURL',
            'vnp_ipn_url'            => 'VNP_IPN_URL',
            'vnp_ngan_hang_mac_dinh' => 'VNP_BANKCODE',

            // GHN
            'ghn_token'           => 'GHN_TOKEN',
            'ghn_shop_id'         => 'GHN_SHOP_ID',
            'ghn_api'             => 'GHN_API_URL',
            'ghn_ten_nguoi_gui'   => 'GHN_PICK_NAME',
            'ghn_sdt_nguoi_gui'   => 'GHN_PICK_TEL',
            'ghn_dia_chi_nhan'    => 'GHN_PICK_ADDRESS',
            'ghn_tinh_thanh'      => 'GHN_PICK_PROVINCE',
            'ghn_quan_huyen'      => 'GHN_PICK_DISTRICT',
            'ghn_ma_quan_huyen'   => 'GHN_PICK_DISTRICT_ID',
            'ghn_phuong_xa_code'  => 'GHN_PICK_WARD_CODE',
            'ghn_can_nang_nguong' => 'GHN_MIN_HEAVY_WEIGHT',
        ];

        // Tách payload ENV theo map tiếng Việt (đồng thời vẫn cho phép key tiếng Anh nếu form cũ)
        $envPayload = [];
        foreach ($vnToEnv as $vn => $envKey) {
            if ($request->has($vn)) {
                $envPayload[$envKey] = $request->input($vn);
            }
        }

        // Nếu bạn vẫn giữ tên tiếng Anh trong form cũ thì merge luôn:
        $fallbackEnvKeys = array_values($vnToEnv);
        foreach ($fallbackEnvKeys as $envKey) {
            if (!array_key_exists($envKey, $envPayload) && $request->has($envKey)) {
                $envPayload[$envKey] = $request->input($envKey);
            }
        }

        // Gộp mail_* nếu có (cũng là ENV)
        $envPayload = array_merge($envPayload, [
            'MAIL_MAILER'       => $envPayload['MAIL_MAILER']       ?? env('MAIL_MAILER', 'smtp'),
            'MAIL_HOST'         => $envPayload['MAIL_HOST']         ?? env('MAIL_HOST'),
            'MAIL_PORT'         => $envPayload['MAIL_PORT']         ?? env('MAIL_PORT'),
            'MAIL_USERNAME'     => $envPayload['MAIL_USERNAME']     ?? env('MAIL_USERNAME'),
            'MAIL_PASSWORD'     => $envPayload['MAIL_PASSWORD']     ?? env('MAIL_PASSWORD'),
            'MAIL_ENCRYPTION'   => $envPayload['MAIL_ENCRYPTION']   ?? env('MAIL_ENCRYPTION'),
            'MAIL_FROM_ADDRESS' => $envPayload['MAIL_FROM_ADDRESS'] ?? env('MAIL_FROM_ADDRESS'),
            'MAIL_FROM_NAME'    => $envPayload['MAIL_FROM_NAME']    ?? env('MAIL_FROM_NAME', 'Laravel'),
        ]);

        // Điền mặc định cho các ENV còn thiếu
        $envPayload += [
            'VNP_URL'        => env('VNP_URL', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html'),
            'VNP_API_URL'    => env('VNP_API_URL', 'https://sandbox.vnpayment.vn/merchant_webapi'),
            'VNP_TMNCODE'    => env('VNP_TMNCODE'),
            'VNP_HASHSECRET' => env('VNP_HASHSECRET'),
            'VNP_RETURNURL'  => env('VNP_RETURNURL'),
            'VNP_IPN_URL'    => env('VNP_IPN_URL'),
            'VNP_BANKCODE'   => env('VNP_BANKCODE', 'NCB'),

            'GHN_TOKEN'            => env('GHN_TOKEN'),
            'GHN_SHOP_ID'          => env('GHN_SHOP_ID'),
            'GHN_API_URL'          => env('GHN_API_URL', 'https://online-gateway.ghn.vn/shiip/public-api'),
            'GHN_PICK_NAME'        => env('GHN_PICK_NAME'),
            'GHN_PICK_TEL'         => env('GHN_PICK_TEL'),
            'GHN_PICK_ADDRESS'     => env('GHN_PICK_ADDRESS'),
            'GHN_PICK_PROVINCE'    => env('GHN_PICK_PROVINCE'),
            'GHN_PICK_DISTRICT'    => env('GHN_PICK_DISTRICT'),
            'GHN_PICK_DISTRICT_ID' => env('GHN_PICK_DISTRICT_ID'),
            'GHN_PICK_WARD_CODE'   => env('GHN_PICK_WARD_CODE'),
            'GHN_MIN_HEAVY_WEIGHT' => env('GHN_MIN_HEAVY_WEIGHT', 1000),
        ];

        // Lưu các key KHÔNG phải ENV (VD: công tắc bật/tắt & thông tin chung)
        foreach ($data as $key => $value) {
            // các tên Việt và tên ảnh không phải ENV
            if (!Str::startsWith($key, ['MAIL_', 'VNP_', 'GHN_'])) {
                Setting::updateOrCreate(['key' => $key], ['value' => $value ?? '']);
            }
        }

        // Ghi ENV
        $this->updateEnvFile($envPayload);

        Artisan::call('config:clear');
        Artisan::call('cache:clear');

        return back()->with('success', 'Cài đặt đã được cập nhật thành công.');
    }

    protected function updateEnvFile(array $data)
    {
        $envFilePath = app()->environmentFilePath();
        $content = file_get_contents($envFilePath);

        foreach ($data as $key => $value) {
            $key = strtoupper($key);
            $value = (string)($value ?? '');
            // Bọc "" nếu có khoảng trắng
            $escapedValue = Str::contains($value, ' ') ? "\"{$value}\"" : $value;

            $escapedKey = preg_quote("{$key}=", '/');
            $pattern = "/^{$escapedKey}.*/m";

            if (preg_match($pattern, $content)) {
                $content = preg_replace($pattern, "{$key}={$escapedValue}", $content);
            } else {
                $content .= "\n{$key}={$escapedValue}";
            }
        }

        file_put_contents($envFilePath, $content);
    }
}
