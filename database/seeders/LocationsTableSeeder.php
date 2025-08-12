<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class LocationsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('locations')->delete();
        
        \DB::table('locations')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'Kho hàng cho biến thể #1',
                'address' => '20a',
                'city_id' => NULL,
                'city_name' => NULL,
                'district_id' => NULL,
                'district_name' => NULL,
                'ward_id' => NULL,
                'ward_name' => NULL,
                'created_at' => '2025-08-06 12:55:44',
                'updated_at' => '2025-08-06 12:55:44',
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'Kho hàng cho biến thể #2',
                'address' => '20a',
                'city_id' => NULL,
                'city_name' => NULL,
                'district_id' => NULL,
                'district_name' => NULL,
                'ward_id' => NULL,
                'ward_name' => NULL,
                'created_at' => '2025-08-06 12:55:45',
                'updated_at' => '2025-08-06 12:55:45',
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'Kho hàng cho biến thể #3',
                'address' => '20a',
                'city_id' => NULL,
                'city_name' => NULL,
                'district_id' => NULL,
                'district_name' => NULL,
                'ward_id' => NULL,
                'ward_name' => NULL,
                'created_at' => '2025-08-06 12:55:45',
                'updated_at' => '2025-08-06 12:55:45',
            ),
            3 => 
            array (
                'id' => 4,
                'name' => 'Kho hàng cho biến thể #4',
                'address' => '20a',
                'city_id' => NULL,
                'city_name' => NULL,
                'district_id' => NULL,
                'district_name' => NULL,
                'ward_id' => NULL,
                'ward_name' => NULL,
                'created_at' => '2025-08-06 12:55:46',
                'updated_at' => '2025-08-06 12:55:46',
            ),
            4 => 
            array (
                'id' => 5,
                'name' => 'Kho hàng cho biến thể #5',
                'address' => '20a',
                'city_id' => NULL,
                'city_name' => NULL,
                'district_id' => NULL,
                'district_name' => NULL,
                'ward_id' => NULL,
                'ward_name' => NULL,
                'created_at' => '2025-08-06 22:42:40',
                'updated_at' => '2025-08-06 22:42:40',
            ),
            5 => 
            array (
                'id' => 6,
                'name' => 'Kho hàng cho biến thể #6',
                'address' => '20a',
                'city_id' => NULL,
                'city_name' => NULL,
                'district_id' => NULL,
                'district_name' => NULL,
                'ward_id' => NULL,
                'ward_name' => NULL,
                'created_at' => '2025-08-06 22:42:41',
                'updated_at' => '2025-08-06 22:42:41',
            ),
            6 => 
            array (
                'id' => 7,
                'name' => 'Kho hàng cho biến thể #7',
                'address' => '20a',
                'city_id' => NULL,
                'city_name' => NULL,
                'district_id' => NULL,
                'district_name' => NULL,
                'ward_id' => NULL,
                'ward_name' => NULL,
                'created_at' => '2025-08-06 22:43:06',
                'updated_at' => '2025-08-06 22:43:06',
            ),
            7 => 
            array (
                'id' => 8,
                'name' => 'Kho hàng cho biến thể #8',
                'address' => '20a',
                'city_id' => NULL,
                'city_name' => NULL,
                'district_id' => NULL,
                'district_name' => NULL,
                'ward_id' => NULL,
                'ward_name' => NULL,
                'created_at' => '2025-08-06 22:43:27',
                'updated_at' => '2025-08-06 22:43:27',
            ),
            8 => 
            array (
                'id' => 9,
                'name' => 'Kho hàng cho biến thể #9',
                'address' => NULL,
                'city_id' => NULL,
                'city_name' => NULL,
                'district_id' => NULL,
                'district_name' => NULL,
                'ward_id' => NULL,
                'ward_name' => NULL,
                'created_at' => '2025-08-09 21:56:48',
                'updated_at' => '2025-08-09 21:56:48',
            ),
            9 => 
            array (
                'id' => 10,
                'name' => 'Kho hàng cho biến thể #10',
                'address' => '20a',
                'city_id' => NULL,
                'city_name' => NULL,
                'district_id' => NULL,
                'district_name' => NULL,
                'ward_id' => NULL,
                'ward_name' => NULL,
                'created_at' => '2025-08-09 21:57:30',
                'updated_at' => '2025-08-09 21:57:30',
            ),
            10 => 
            array (
                'id' => 11,
                'name' => 'Kho hàng cho biến thể #11',
                'address' => '20a',
                'city_id' => NULL,
                'city_name' => NULL,
                'district_id' => NULL,
                'district_name' => NULL,
                'ward_id' => NULL,
                'ward_name' => NULL,
                'created_at' => '2025-08-09 21:57:30',
                'updated_at' => '2025-08-09 21:57:30',
            ),
            11 => 
            array (
                'id' => 12,
                'name' => 'Kho hàng cho biến thể #12',
                'address' => '20a',
                'city_id' => NULL,
                'city_name' => NULL,
                'district_id' => NULL,
                'district_name' => NULL,
                'ward_id' => NULL,
                'ward_name' => NULL,
                'created_at' => '2025-08-09 21:57:31',
                'updated_at' => '2025-08-09 21:57:31',
            ),
            12 => 
            array (
                'id' => 13,
                'name' => 'Kho hàng cho biến thể #13',
                'address' => '20a',
                'city_id' => NULL,
                'city_name' => NULL,
                'district_id' => NULL,
                'district_name' => NULL,
                'ward_id' => NULL,
                'ward_name' => NULL,
                'created_at' => '2025-08-09 21:57:31',
                'updated_at' => '2025-08-09 21:57:31',
            ),
        ));
        
        
    }
}