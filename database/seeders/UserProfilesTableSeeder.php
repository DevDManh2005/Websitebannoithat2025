<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UserProfilesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('user_profiles')->delete();
        
        \DB::table('user_profiles')->insert(array (
            0 => 
            array (
                'id' => 1,
                'user_id' => 1,
                'avatar' => 'avatars/kggHBrKAjvSddMxcOi3o9DhPEj21wsZmxqQ5PgZe.png',
                'dob' => '2013-06-29',
                'gender' => 'Nam',
                'address' => NULL,
                'created_at' => '2025-08-06 13:14:53',
                'updated_at' => '2025-08-09 21:25:39',
                'province_id' => NULL,
                'district_id' => NULL,
                'ward_id' => NULL,
                'province_name' => 'Đà Nẵng',
                'district_name' => 'Quận Thanh Khê',
                'ward_name' => 'Phường Tân Chính',
            ),
        ));
        
        
    }
}