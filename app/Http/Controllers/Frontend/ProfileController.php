<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Hiển thị trang thông tin tài khoản của người dùng.
     *
     * @return \Illuminate\View\View
     */
    public function show()
    {
        $user = Auth::user()->load('profile');
        return view('frontend.profile.show', compact('user'));
    }

    /**
     * Cập nhật thông tin người dùng.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'dob' => 'nullable|date|before:today',
            'gender' => ['nullable', Rule::in(['Nam', 'Nữ', 'Khác'])],
            'address' => 'nullable|string|max:1000',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'avatar_url' => 'nullable|url'
        ]);

        $profileData = $request->only([
            'dob',
            'gender',
            'address',
            'province_name',
            'district_name',
            'ward_name'
        ]);

        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $profileData['avatar'] = $path;
        } elseif ($request->filled(key: 'avatar_url')) {
            $profileData['avatar'] = $request->avatar_url;
        }

        $user->update(['name' => $request->name]);

        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            $profileData
        );

        return redirect()->route('profile.show')->with('success', 'Cập nhật thông tin thành công!');
    }
    
    /**
     * Thay đổi mật khẩu của người dùng.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (!Hash::check($value, Auth::user()->password)) {
                        $fail('Mật khẩu hiện tại không chính xác.');
                    }
                },
            ],
            'new_password' => [
                'required',
                Password::min(8)->letters()->numbers(),
                'confirmed',
            ],
        ], [
            'new_password.required' => 'Vui lòng nhập mật khẩu mới.',
            'new_password.min' => 'Mật khẩu mới phải có ít nhất 8 ký tự.',
            'new_password.letters' => 'Mật khẩu mới phải chứa ký tự chữ.',
            'new_password.numbers' => 'Mật khẩu mới phải chứa ký tự số.',
            'new_password.confirmed' => 'Xác nhận mật khẩu không khớp.',
        ]);

        $user = Auth::user();
        $user->password = Hash::make($request->new_password);
        $user->save();

        return redirect()->route('profile.show')->with('success', 'Đổi mật khẩu thành công!');
    }
}