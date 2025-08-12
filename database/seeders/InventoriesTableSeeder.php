<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class InventoriesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('inventories')->delete();
        
        \DB::table('inventories')->insert(array (
            0 => 
            array (
                'id' => 6,
                'product_id' => 2,
                'product_variant_id' => 6,
                'quantity' => 20,
                'location_id' => 6,
                'created_at' => '2025-08-06 21:39:06',
                'updated_at' => '2025-08-06 22:42:42',
            ),
            1 => 
            array (
                'id' => 8,
                'product_id' => 4,
                'product_variant_id' => 8,
                'quantity' => 20,
                'location_id' => 8,
                'created_at' => '2025-08-06 22:41:17',
                'updated_at' => '2025-08-06 22:43:28',
            ),
            2 => 
            array (
                'id' => 9,
                'product_id' => 3,
                'product_variant_id' => 9,
                'quantity' => 20,
                'location_id' => 9,
                'created_at' => '2025-08-09 21:56:48',
                'updated_at' => '2025-08-09 21:56:48',
            ),
            3 => 
            array (
                'id' => 10,
                'product_id' => 1,
                'product_variant_id' => 10,
                'quantity' => 20,
                'location_id' => 10,
                'created_at' => '2025-08-09 21:57:30',
                'updated_at' => '2025-08-09 21:57:30',
            ),
            4 => 
            array (
                'id' => 11,
                'product_id' => 1,
                'product_variant_id' => 11,
                'quantity' => 20,
                'location_id' => 11,
                'created_at' => '2025-08-09 21:57:31',
                'updated_at' => '2025-08-09 21:57:31',
            ),
            5 => 
            array (
                'id' => 12,
                'product_id' => 1,
                'product_variant_id' => 12,
                'quantity' => 20,
                'location_id' => 12,
                'created_at' => '2025-08-09 21:57:31',
                'updated_at' => '2025-08-09 21:57:31',
            ),
            6 => 
            array (
                'id' => 13,
                'product_id' => 1,
                'product_variant_id' => 13,
                'quantity' => 20,
                'location_id' => 13,
                'created_at' => '2025-08-09 21:57:32',
                'updated_at' => '2025-08-09 21:57:32',
            ),
            7 => 
            array (
                'id' => 5,
                'product_id' => 2,
                'product_variant_id' => 5,
                'quantity' => 50,
                'location_id' => 5,
                'created_at' => '2025-08-06 21:39:05',
                'updated_at' => '2025-08-11 12:32:49',
            ),
        ));
        
        
    }
}