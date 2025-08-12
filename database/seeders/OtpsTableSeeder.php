<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class OtpsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('otps')->delete();
        
        \DB::table('otps')->insert(array (
            0 => 
            array (
                'id' => 1,
                'email' => 'admin@gmail.com',
                'code' => '168041',
                'type' => 'reset_password',
                'expired_at' => '2025-08-10 13:14:01',
                'created_at' => '2025-08-10 13:04:01',
                'updated_at' => '2025-08-10 13:04:01',
            ),
        ));
        
        
    }
}