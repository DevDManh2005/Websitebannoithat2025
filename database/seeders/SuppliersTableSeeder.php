<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class SuppliersTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('suppliers')->delete();
        
        \DB::table('suppliers')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'BRICK',
                'contact_name' => 'BRICK-QuanLy',
                'phone' => '0865024472',
                'email' => 'support@brick.vn.com',
                'address' => 'Đà Nẵng',
                'is_active' => true,
                'created_at' => '2025-08-06 12:38:31',
                'updated_at' => '2025-08-06 12:38:31',
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'HOME BUILD',
                'contact_name' => 'QuanLy-HomeBuild',
                'phone' => '0865022473',
                'email' => 'support@HomeBuild.vn.com',
                'address' => 'Đồng Nai',
                'is_active' => true,
                'created_at' => '2025-08-06 12:39:19',
                'updated_at' => '2025-08-06 12:39:19',
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'VIRTUPS',
                'contact_name' => 'QuanLy-Virtups',
                'phone' => '0865022432',
                'email' => 'support@VIRTUPS.com.vn',
                'address' => 'Hồ Chí Minh',
                'is_active' => true,
                'created_at' => '2025-08-06 12:40:38',
                'updated_at' => '2025-08-06 12:40:38',
            ),
            3 => 
            array (
                'id' => 4,
                'name' => 'ARCHITECTURE',
                'contact_name' => 'QuanLy-architecture',
                'phone' => '0564987712',
                'email' => 'support@architecture.com.vn',
                'address' => 'Hà Nội',
                'is_active' => true,
                'created_at' => '2025-08-06 12:41:28',
                'updated_at' => '2025-08-06 12:41:28',
            ),
            4 => 
            array (
                'id' => 5,
                'name' => 'CONSTRUCTION',
                'contact_name' => 'Quanly-construction',
                'phone' => '0865023321',
                'email' => 'support@construction.vn.com',
                'address' => 'Thanh Hóa',
                'is_active' => true,
                'created_at' => '2025-08-06 12:42:28',
                'updated_at' => '2025-08-06 12:42:28',
            ),
            5 => 
            array (
                'id' => 6,
                'name' => 'HOME&GARDEN',
                'contact_name' => 'QuanLy-Homegarden',
                'phone' => '0564187719',
                'email' => 'support@Homegarden.vn.com',
                'address' => 'Nghệ An',
                'is_active' => true,
                'created_at' => '2025-08-06 12:43:10',
                'updated_at' => '2025-08-06 12:43:10',
            ),
        ));
        
        
    }
}