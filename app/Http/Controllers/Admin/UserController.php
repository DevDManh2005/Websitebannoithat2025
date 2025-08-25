<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Danh sách người dùng (role = user)
     */
    public function index()
    {
        $users = User::with('profile')
            ->where('role_id', 3)
            ->paginate(10);

        return view('admins.users.index', compact('users'));
    }

    /**
     * Form chỉnh sửa thông tin user + profile
     */
    public function edit(User $user)
    {
        // Không cần truyền dữ liệu địa chỉ từ đây nữa, JS sẽ tự gọi API
        return view('admins.users.edit', compact('user'));
    }

    /**
     * Xử lý cập nhật cả users và user_profiles
     */
    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            // user
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|confirmed|min:6',
            // profile
            'dob' => 'nullable|date',
            'gender' => 'nullable|string',
            'province_name' => 'nullable|string',
            'district_name' => 'nullable|string',
            'ward_name' => 'nullable|string',
            'address' => 'nullable|string',
        ]);

        // 1) Update bảng users
        $user->name = $data['name'];
        $user->email = $data['email'];
        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }
        $user->save();

        // 2) UpdateOrCreate profile
        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'dob' => $data['dob'] ?? null,
                'gender' => $data['gender'] ?? null,
                'province_name' => $data['province_name'] ?? null,
                'district_name' => $data['district_name'] ?? null,
                'ward_name' => $data['ward_name'] ?? null,
                'address' => $data['address'] ?? null,
            ]
        );


        return redirect()->route('admin.users.index')
            ->with('success', 'Cập nhật thành công');
    }

    /**
     * Khoá / Mở khoá tài khoản
     */
    public function toggleLock(User $user)
    {
        $user->is_active = !$user->is_active;
        $user->save();


        return back()->with('success', 'Đã ' . ($user->is_active ? 'mở khoá' : 'khoá') . ' tài khoản');
    }

    /**
     * Xem chi tiết người dùng
     */
    public function show(User $user)
    {
        $user->load('profile');

        return view('admins.users.show', compact('user'));
    }
}
