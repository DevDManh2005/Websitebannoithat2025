<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\{
    Hash, Auth, Mail, Session
};
use App\Models\{User, Otp};
use App\Mail\{SendVerifyOtpMail, SendResetOtpMail};
use Carbon\Carbon;

use App\Http\Requests\{
    RegisterRequest,
    VerifyOtpRequest,
    LoginRequest,
    ForgotRequest,
    ResetPasswordRequest
};

class AuthController extends Controller
{
    /**
     * 1. ĐĂNG KÝ – Gán role_id mặc định là 3 (User)
     */
    public function register(RegisterRequest $request)
    {
        $data = $request->validated();

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'role_id'  => 3,
        ]);

        // Tạo và gửi OTP xác minh email
        $otpCode = rand(100000, 999999);
        Otp::create([
            'email'      => $data['email'],
            'code'       => $otpCode,
            'type'       => 'verify_email',
            'expired_at' => now()->addMinutes(10),
        ]);
        Mail::to($data['email'])->send(new SendVerifyOtpMail($otpCode));

        // Lưu vào session để view verify dùng chung
        session([
            'otp_email' => $data['email'],
            'otp_type'  => 'verify_email'
        ]);

        return redirect()->route('verify.form')
                         ->with('success', 'Đăng ký thành công. Vui lòng kiểm tra email để nhập mã OTP.');
    }

    /**
     * 2. XÁC MINH OTP (Email & Quên mật khẩu dùng chung view)
     */
    public function verifyOTP(VerifyOtpRequest $request)
    {
        $data = $request->validated();
        $type = session('otp_type', 'verify_email');

        $otp = Otp::where('email', $data['email'])
            ->where('code', $data['code'])
            ->where('type', $type)
            ->where('expired_at', '>', now())
            ->latest()->first();

        if (!$otp) {
            return back()->withErrors(['code' => 'Mã OTP không hợp lệ hoặc đã hết hạn.']);
        }

        if ($type === 'verify_email') {
            User::where('email', $data['email'])
                ->update(['email_verified_at' => now()]);
            $message = 'Xác minh email thành công. Mời bạn đăng nhập.';
        } else {
            $message = 'Xác thực OTP thành công. Mời bạn đặt lại mật khẩu.';
        }

        $otp->delete();

        // Sau verify_email chuyển sang login, sau reset_password vẫn ở form reset để nhập password
        return $type === 'verify_email'
            ? redirect()->route('login.form')->with('success', $message)
            : redirect()->route('reset.form')->with('success', $message);
    }

    /**
     * 3. ĐĂNG NHẬP – Kiểm tra xác minh email và điều hướng theo role
     */
    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();

        if (!Auth::attempt($credentials)) {
            return back()->withErrors(['email' => 'Thông tin đăng nhập không đúng.']);
        }

        $user = Auth::user();

        if (!$user->email_verified_at) {
            Auth::logout();
            session([
                'otp_email' => $user->email,
                'otp_type'  => 'verify_email'
            ]);
            return redirect()->route('verify.form')
                             ->withErrors(['email' => 'Vui lòng xác minh email trước khi đăng nhập.']);
        }

        return redirect()->route('home')->with('success', 'Đăng nhập thành công!');
    }

    /**
     * 4. QUÊN MẬT KHẨU – Gửi mã OTP
     */
    public function forgot(ForgotRequest $request)
    {
        $data = $request->validated();

        // Kiểm tra email có tồn tại?
        if (!User::where('email', $data['email'])->exists()) {
            return back()->withErrors(['email' => 'Email này chưa đăng ký trong hệ thống.']);
        }

        $otpCode = rand(100000, 999999);
        Otp::create([
            'email'      => $data['email'],
            'code'       => $otpCode,
            'type'       => 'reset_password',
            'expired_at' => now()->addMinutes(10),
        ]);
        Mail::to($data['email'])->send(new SendResetOtpMail($otpCode));

        session([
            'otp_email' => $data['email'],
            'otp_type'  => 'reset_password'
        ]);

        return redirect()->route('reset.form')
                         ->with('success', 'Đã gửi mã OTP khôi phục mật khẩu đến email của bạn.');
    }

    /**
     * 5. ĐẶT LẠI MẬT KHẨU
     */
    public function resetPassword(ResetPasswordRequest $request)
    {
        $data = $request->validated();

        $otp = Otp::where('email', $data['email'])
            ->where('code', $data['code'])
            ->where('type', 'reset_password')
            ->where('expired_at', '>', now())
            ->latest()->first();

        if (!$otp) {
            return back()->withErrors(['code' => 'Mã OTP không hợp lệ hoặc đã hết hạn.']);
        }

        User::where('email', $data['email'])
            ->update(['password' => Hash::make($data['password'])]);

        $otp->delete();

        return redirect()->route('login.form')
                         ->with('success', 'Mật khẩu đã được thay đổi thành công. Vui lòng đăng nhập lại.');
    }

    /**
     * 6. GỬI LẠI MÃ OTP
     */
    public function resendOtp()
    {
        $email = session('otp_email');
        $type  = session('otp_type', 'verify_email');

        if (!$email) {
            return back()->withErrors(['code' => 'Không thể gửi lại mã OTP. Vui lòng thử lại.']);
        }

        $otpCode = rand(100000, 999999);
        Otp::create([
            'email'      => $email,
            'code'       => $otpCode,
            'type'       => $type,
            'expired_at' => now()->addMinutes(10),
        ]);

        $mail = $type === 'verify_email'
            ? new SendVerifyOtpMail($otpCode)
            : new SendResetOtpMail($otpCode);

        Mail::to($email)->send($mail);

        return back()->with('success', 'Mã OTP đã được gửi lại thành công.');
    }

    /**
     * 7. ĐĂNG XUẤT
     */
    public function logout()
    {
        Auth::logout();
        return redirect()->route('login.form')
                         ->with('success', 'Bạn đã đăng xuất thành công.');
    }
}
