<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CategoriesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('categories')->delete();
        
        \DB::table('categories')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'Văn Phòng',
                'slug' => 'van-phong',
                'parent_id' => NULL,
                'is_active' => true,
                'position' => 0,
                'created_at' => '2025-08-06 12:23:59',
                'updated_at' => '2025-08-06 12:23:59',
                'image' => 'categories/sQAO91ZNMcDRqJmCvMzCBiMdyMLVFqSCGSAmK75R.jpg',
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'Phòng Khách',
                'slug' => 'phong-khach',
                'parent_id' => NULL,
                'is_active' => true,
                'position' => 0,
                'created_at' => '2025-08-06 12:25:02',
                'updated_at' => '2025-08-06 12:25:02',
                'image' => 'categories/AiCcmXzyX5lQWnxHxB5BHc0BXB0tuqrsUowk0Qvr.jpg',
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'Phòng Ngủ',
                'slug' => 'phong-ngu',
                'parent_id' => NULL,
                'is_active' => true,
                'position' => 0,
                'created_at' => '2025-08-06 12:25:53',
                'updated_at' => '2025-08-06 12:25:53',
                'image' => 'categories/Kexytmen3lX9kWCZF3BwcOOpgiHBCwsLATtcuA0f.jpg',
            ),
            3 => 
            array (
                'id' => 4,
                'name' => 'Phòng Bếp',
                'slug' => 'phong-bep',
                'parent_id' => NULL,
                'is_active' => true,
                'position' => 0,
                'created_at' => '2025-08-06 12:26:22',
                'updated_at' => '2025-08-06 12:26:22',
                'image' => 'categories/z9IzYDfWCFW9eskh2vG1OEKx0sHdDZG0TD2ik5t3.webp',
            ),
            4 => 
            array (
                'id' => 5,
                'name' => 'Ghế Sofa',
                'slug' => 'ghe-sofa',
                'parent_id' => 2,
                'is_active' => true,
                'position' => 1,
                'created_at' => '2025-08-06 12:44:38',
                'updated_at' => '2025-08-06 12:44:38',
                'image' => 'categories/2ypmlIhjGDg1epPVukcYs9FdG4UiNXHaNSm5AfLv.jpg',
            ),
            5 => 
            array (
                'id' => 6,
                'name' => 'Bàn Ăn',
                'slug' => 'ban-an',
                'parent_id' => 4,
                'is_active' => true,
                'position' => 1,
                'created_at' => '2025-08-06 12:46:36',
                'updated_at' => '2025-08-06 12:46:36',
                'image' => 'categories/sNFkFCjsTOfQwLAWEiOHQdaxMfgCzo5NhdZPgZRT.webp',
            ),
            6 => 
            array (
                'id' => 7,
                'name' => 'Giường Ngủ',
                'slug' => 'giuong-ngu',
                'parent_id' => 3,
                'is_active' => true,
                'position' => 1,
                'created_at' => '2025-08-06 12:47:07',
                'updated_at' => '2025-08-06 12:47:07',
                'image' => 'categories/IoI0wZkC5lwPFuXewhPBnMSuxjdDxWdXaSEl1n6z.jpg',
            ),
            7 => 
            array (
                'id' => 8,
                'name' => 'Bàn Làm Việc',
                'slug' => 'ban-lam-viec',
                'parent_id' => 1,
                'is_active' => true,
                'position' => 1,
                'created_at' => '2025-08-06 12:47:51',
                'updated_at' => '2025-08-06 12:47:51',
                'image' => 'categories/dibfat9TYz2zPNDspNnrh5HNlclucJsigVituDC1.jpg',
            ),
        ));
        
        
    }
}