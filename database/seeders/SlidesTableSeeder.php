<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class SlidesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('slides')->delete();
        
        \DB::table('slides')->insert(array (
            0 => 
            array (
                'id' => 1,
                'title' => 'Chào Mừng Bạn Đã Đến Với Website EternaHome',
                'subtitle' => 'ETERNAL HOME - CHUYÊN CUNG CẤP CÁC SẢN PHẨM NỘI THẤT CHO CĂN NHÀ CỦA BẠN',
                'image' => 'slides/7dAyUIh6NwaZLRnNEUDbQrx8A2Wgqcgjk1OeVaNe.jpg',
                'button_text' => 'XEM CÁC SẢN PHẨM',
                'button_link' => 'https://64b6b175b666.ngrok-free.app/san-pham',
                'position' => 0,
                'is_active' => true,
                'created_at' => '2025-08-06 12:21:00',
                'updated_at' => '2025-08-06 12:21:00',
            ),
            1 => 
            array (
                'id' => 2,
                'title' => 'Chào Mừng Bạn Đã Đến Với Eternal Home',
                'subtitle' => 'ETERNAL HOME CHUYÊN CUNG CẤP CÁC SẢN PHẨM NỘI THẤT CHO CĂN NHÀ CỦA BẠN',
                'image' => 'slides/mbFH00QqrcxzbD8N8xgeZ0leY1YiPQTYda6MnSVe.jpg',
                'button_text' => 'XEM CÁC SẢN PHẨM',
                'button_link' => 'https://64b6b175b666.ngrok-free.app/san-pham',
                'position' => 0,
                'is_active' => true,
                'created_at' => '2025-08-06 12:22:39',
                'updated_at' => '2025-08-06 12:22:39',
            ),
        ));
        
        
    }
}