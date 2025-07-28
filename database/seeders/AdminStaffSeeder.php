<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminStaffSeeder extends Seeder
{
    public function run()
    {
        // Tạo Admin
        User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name'              => 'Administrator',
                'password'          => Hash::make('Admin@123'), // nhớ đổi password sao cho mạnh
                'role_id'           => 1,                       // theo seeder RoleSeeder
                'email_verified_at' => now(),
            ]
        );

        // Tạo Staff
        User::updateOrCreate(
            ['email' => 'staff@gmail.com'],
            [
                'name'              => 'Staff Member',
                'password'          => Hash::make('Staff@123'),
                'role_id'           => 2,
                'email_verified_at' => now(),
            ]
        );
    }
}
