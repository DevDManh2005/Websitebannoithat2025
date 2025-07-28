<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run()
    {
        $roles = [
            1 => 'admin',
            2 => 'staff',
            3 => 'user',
        ];

        foreach ($roles as $id => $name) {
            Role::updateOrCreate(
                ['id' => $id],
                ['name' => $name]
            );
        }
    }
}
