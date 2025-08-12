<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class SettingsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('settings')->delete();
        
        \DB::table('settings')->insert(array (
            0 => 
            array (
                'id' => 1,
                'key' => 'site_name',
                'value' => 'EternaHome',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'key' => 'site_description',
                'value' => '',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            2 => 
            array (
                'id' => 3,
                'key' => 'site_keywords',
                'value' => '',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            3 => 
            array (
                'id' => 11,
                'key' => 'logo_light',
                'value' => 'settings/WFixdtgcc8AsXEZ7mDnBsaQ0BbcJGui1wb45GPXx.png',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            4 => 
            array (
                'id' => 12,
                'key' => 'logo_dark',
                'value' => 'settings/YTik0e5Kw6ZwTMuMkUlcSMY6OUjK5oVjtyNGn69q.png',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            5 => 
            array (
                'id' => 13,
                'key' => 'favicon',
                'value' => 'settings/kmIsNplhjhDGCN3I2TaTYxH552oWXYfEjEA2hDTx.png',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            6 => 
            array (
                'id' => 4,
                'key' => 'contact_phone',
                'value' => '0865024471',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            7 => 
            array (
                'id' => 5,
                'key' => 'contact_email',
                'value' => 'manhldpd10554@gmail.com',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            8 => 
            array (
                'id' => 6,
                'key' => 'contact_address',
                'value' => 'Hòa Hiệp Nam , Liên Chiểu , Đà Nẵng',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            9 => 
            array (
                'id' => 7,
                'key' => 'social_facebook',
                'value' => 'https://www.facebook.com/manhhnee2005',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            10 => 
            array (
                'id' => 8,
                'key' => 'social_instagram',
                'value' => 'https://www.facebook.com/manhhnee2005',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            11 => 
            array (
                'id' => 9,
                'key' => 'social_x',
                'value' => 'https://www.facebook.com/manhhnee2005',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            12 => 
            array (
                'id' => 10,
                'key' => 'social_tiktok',
                'value' => 'https://www.facebook.com/manhhnee2005',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
        ));
        
        
    }
}