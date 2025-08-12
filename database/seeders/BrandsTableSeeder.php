<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class BrandsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('brands')->delete();
        
        \DB::table('brands')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'BRICK',
                'slug' => 'brick',
                'logo' => 'https://bizweb.dktcdn.net/100/570/902/themes/1027061/assets/logo_brand1.jpg?1753780953304',
                'is_active' => true,
                'created_at' => '2025-08-06 12:30:45',
                'updated_at' => '2025-08-06 12:30:45',
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'Home Build',
                'slug' => 'home-build',
                'logo' => 'https://bizweb.dktcdn.net/100/570/902/themes/1027061/assets/logo_brand2.jpg?1753780953304',
                'is_active' => true,
                'created_at' => '2025-08-06 12:31:26',
                'updated_at' => '2025-08-06 12:31:26',
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'VIRTUPS',
                'slug' => 'virtups',
                'logo' => 'https://bizweb.dktcdn.net/100/570/902/themes/1027061/assets/logo_brand3.jpg?1753780953304',
                'is_active' => true,
                'created_at' => '2025-08-06 12:32:06',
                'updated_at' => '2025-08-06 12:32:06',
            ),
            3 => 
            array (
                'id' => 4,
                'name' => 'ARCHITECTURE',
                'slug' => 'architecture',
                'logo' => 'https://bizweb.dktcdn.net/100/570/902/themes/1027061/assets/logo_brand4.jpg?1753780953304',
                'is_active' => true,
                'created_at' => '2025-08-06 12:35:52',
                'updated_at' => '2025-08-06 12:35:52',
            ),
            4 => 
            array (
                'id' => 5,
                'name' => 'CONSTRUCTION',
                'slug' => 'construction',
                'logo' => 'https://bizweb.dktcdn.net/100/570/902/themes/1027061/assets/logo_brand5.jpg?1753780953304',
                'is_active' => true,
                'created_at' => '2025-08-06 12:36:22',
                'updated_at' => '2025-08-06 12:36:22',
            ),
            5 => 
            array (
                'id' => 6,
                'name' => 'HOME&GARDEN',
                'slug' => 'homegarden',
                'logo' => 'https://bizweb.dktcdn.net/100/570/902/themes/1027061/assets/logo_brand6.jpg?1753780953304',
                'is_active' => true,
                'created_at' => '2025-08-06 12:36:59',
                'updated_at' => '2025-08-06 12:36:59',
            ),
        ));
        
        
    }
}