<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('users')->delete();
        
        \DB::table('users')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'Administrator',
                'email' => 'admin@gmail.com',
                'email_verified_at' => '2025-08-06 12:15:23',
                'password' => '$2y$12$2mB6SvjDdV/Z9iWDQDl9GOWqoDNCcOiTNzFPEgyZ3h22IpSUXmYRG',
                'role_id' => 1,
                'remember_token' => NULL,
                'created_at' => '2025-08-06 12:15:23',
                'updated_at' => '2025-08-06 12:15:23',
                'is_active' => true,
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'Staff Member',
                'email' => 'staff@gmail.com',
                'email_verified_at' => '2025-08-06 12:15:24',
                'password' => '$2y$12$hPCtauqHrF/mf.51KkObi.4SLxVUfaTh88LY51mIDJpc54wxy9t/K',
                'role_id' => 2,
                'remember_token' => NULL,
                'created_at' => '2025-08-06 12:15:24',
                'updated_at' => '2025-08-06 12:15:24',
                'is_active' => true,
            ),
        ));
        
        
    }
}