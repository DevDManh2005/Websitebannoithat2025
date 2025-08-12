<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ProductImagesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('product_images')->delete();
        
        \DB::table('product_images')->insert(array (
            0 => 
            array (
                'id' => 3,
                'product_id' => 2,
                'image_url' => 'https://product.hstatic.net/200000065946/product/pro_nau_noi_that_moho_ban_an_go_vline_1_2070a5751a624ebcb4ff1a0f726b74c2_master.jpg',
                'is_primary' => true,
                'created_at' => '2025-08-06 21:39:05',
                'updated_at' => '2025-08-06 21:39:05',
            ),
            1 => 
            array (
                'id' => 4,
                'product_id' => 2,
                'image_url' => 'https://product.hstatic.net/200000065946/product/pro_nau_noi_that_moho_ban_an___2__4d15bb4d1a774a4e891415feeae1fe25_master.png',
                'is_primary' => false,
                'created_at' => '2025-08-06 21:39:05',
                'updated_at' => '2025-08-06 21:39:05',
            ),
            2 => 
            array (
                'id' => 7,
                'product_id' => 4,
                'image_url' => 'https://product.hstatic.net/200000065946/product/pro_nau_noi_that_moho_ban_lam_viec_vline_601_a_e60e2f8b72854311ae12424eed3cb88a_master.jpg',
                'is_primary' => true,
                'created_at' => '2025-08-06 22:41:16',
                'updated_at' => '2025-08-06 22:41:16',
            ),
            3 => 
            array (
                'id' => 8,
                'product_id' => 4,
                'image_url' => 'https://product.hstatic.net/200000065946/product/pro_nau_noi_that_moho_ban_lam_viec_vline_601_45d5b92f9a2b464e8382610013e531d7_master.jpg',
                'is_primary' => false,
                'created_at' => '2025-08-06 22:41:16',
                'updated_at' => '2025-08-06 22:41:16',
            ),
            4 => 
            array (
                'id' => 9,
                'product_id' => 3,
                'image_url' => 'https://cdn.hstatic.net/products/200000065946/pro_mau_tu_nhien_ghe_sofa_1m6_narvik_noi_that_moho_1f112f84fee3476a816fb1a98a35c05c_master.jpg',
                'is_primary' => true,
                'created_at' => '2025-08-09 21:55:03',
                'updated_at' => '2025-08-09 21:55:03',
            ),
            5 => 
            array (
                'id' => 10,
                'product_id' => 1,
                'image_url' => 'https://product.hstatic.net/200000065946/product/pro_nau_noi_that_moho_giuong_ngu_go_tram_vline_1m8_a_6ba57dbc2c7943509208badc020decf8_master.jpg',
                'is_primary' => true,
                'created_at' => '2025-08-09 21:55:36',
                'updated_at' => '2025-08-09 21:55:36',
            ),
        ));
        
        
    }
}