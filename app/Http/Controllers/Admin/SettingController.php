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

        // Xử lý upload file
        $filesToUpload = ['logo_light', 'logo_dark', 'favicon', 'og_image'];
        foreach ($filesToUpload as $key) {
            if ($request->hasFile($key)) {
                $oldPath = Setting::where('key', $key)->value('value');
                if ($oldPath) {
                    Storage::disk('public')->delete($oldPath);
                }
                $path = $request->file($key)->store('settings', 'public');
                $data[$key] = $path;
            }
        }

        // Xử lý xóa file cũ nếu người dùng nhấn nút "Xóa"
        if ($request->has('remove_image')) {
            foreach ($request->input('remove_image') as $keyToRemove) {
                $oldPath = Setting::where('key', $keyToRemove)->value('value');
                if ($oldPath) {
                    Storage::disk('public')->delete($oldPath);
                }
                $data[$keyToRemove] = null; // Xóa đường dẫn trong DB
            }
        }

        // Lưu các cài đặt không phải mail vào database
        foreach ($data as $key => $value) {
            if (!Str::startsWith($key, 'mail_')) {
                Setting::updateOrCreate(['key' => $key], ['value' => $value ?? '']);
            }
        }

        // Cập nhật file .env cho cấu hình SMTP
        $this->updateEnvFile([
            'MAIL_MAILER' => $data['mail_mailer'] ?? 'smtp',
            'MAIL_HOST' => $data['mail_host'] ?? '',
            'MAIL_PORT' => $data['mail_port'] ?? '',
            'MAIL_USERNAME' => $data['mail_username'] ?? '',
            'MAIL_PASSWORD' => $data['mail_password'] ?? '',
            'MAIL_ENCRYPTION' => $data['mail_encryption'] ?? '',
            'MAIL_FROM_ADDRESS' => $data['mail_from_address'] ?? '',
            'MAIL_FROM_NAME' => $data['mail_from_name'] ?? 'Laravel',
        ]);
        
        Artisan::call('config:clear');
        Artisan::call('cache:clear'); // Thêm xóa cache chung

        return back()->with('success', 'Cài đặt đã được cập nhật thành công.');
    }

    protected function updateEnvFile(array $data)
    {
        $envFilePath = app()->environmentFilePath();
        $content = file_get_contents($envFilePath);
        foreach ($data as $key => $value) {
            $key = strtoupper($key);
            $escapedValue = '"' . addslashes($value) . '"';
            
            if (Str::contains($content, "^{$key}=", true)) {
                 $content = preg_replace("/^{$key}=.*/m", "{$key}={$escapedValue}", $content);
            } else {
                 $content .= "\n{$key}={$escapedValue}";
            }
        }
        file_put_contents($envFilePath, $content);
    }
}