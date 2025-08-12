<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ProductVariantsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('product_variants')->delete();
        
        \DB::table('product_variants')->insert(array (
            0 => 
            array (
                'id' => 5,
                'product_id' => 2,
                'is_main_variant' => true,
                'price' => '2990000.00',
                'sale_price' => '2199000.00',
                'created_at' => '2025-08-06 21:39:05',
                'updated_at' => '2025-08-06 21:39:05',
                'sku' => 'MFDTCA701.B09',
                'attributes' => '{"M\\u00e0u":"N\\u00e2u","K\\u00edch Th\\u01b0\\u1edbc":"D\\u00e0i 90cm x R\\u1ed9ng 75cm x Cao 65cm"}',
                'weight' => 2000,
            ),
            1 => 
            array (
                'id' => 6,
                'product_id' => 2,
                'is_main_variant' => false,
                'price' => '2990000.00',
                'sale_price' => '2199000.00',
                'created_at' => '2025-08-06 21:39:05',
                'updated_at' => '2025-08-06 21:39:05',
                'sku' => 'MFDTCA701.B10',
                'attributes' => '{"M\\u00e0u":"T\\u1ef1 Nhi\\u00ean","K\\u00edch Th\\u01b0\\u1edbc":"D\\u00e0i 90cm x R\\u1ed9ng 75cm x Cao 65cm"}',
                'weight' => 2000,
            ),
            2 => 
            array (
                'id' => 8,
                'product_id' => 4,
                'is_main_variant' => true,
                'price' => '1990000.00',
                'sale_price' => NULL,
                'created_at' => '2025-08-06 22:41:16',
                'updated_at' => '2025-08-06 22:41:16',
                'sku' => 'MFTNCAO01.B11',
                'attributes' => '{"M\\u00e0u":"N\\u00e2u","K\\u00edch Th\\u01b0\\u1edbc":"D\\u00e0i 110cm x R\\u1ed9ng 55cm x Cao 74cm"}',
                'weight' => 5000,
            ),
            3 => 
            array (
                'id' => 9,
                'product_id' => 3,
                'is_main_variant' => true,
                'price' => '9900000.00',
                'sale_price' => '7490000.00',
                'created_at' => '2025-08-09 21:55:03',
                'updated_at' => '2025-08-09 21:55:03',
                'sku' => 'MFSNCF601.N16',
                'attributes' => '{"M\\u00e0u":"M\\u00e0u X\\u00e1m","K\\u00edch Th\\u01b0\\u1edbc":"D\\u00e0i 160 \\u00d7 R\\u1ed9ng 81 \\u00d7 Cao 81 cm"}',
                'weight' => 9000,
            ),
            4 => 
            array (
                'id' => 10,
                'product_id' => 1,
                'is_main_variant' => true,
                'price' => '7990000.00',
                'sale_price' => '5790000.00',
                'created_at' => '2025-08-09 21:55:35',
                'updated_at' => '2025-08-09 21:55:35',
                'sku' => 'MFBNCBD04 B1',
                'attributes' => '{"K\\u00edch th\\u01b0\\u1edbc":"1m2","M\\u00e0u":"N\\u00e2u"}',
                'weight' => 5000,
            ),
            5 => 
            array (
                'id' => 11,
                'product_id' => 1,
                'is_main_variant' => false,
                'price' => '8990000.00',
                'sale_price' => '6290000.00',
                'created_at' => '2025-08-09 21:55:35',
                'updated_at' => '2025-08-09 21:55:35',
                'sku' => 'MFBNCBD04 B2',
                'attributes' => '{"K\\u00edch th\\u01b0\\u1edbc":"1m4","M\\u00e0u":"N\\u00e2u"}',
                'weight' => 5000,
            ),
            6 => 
            array (
                'id' => 12,
                'product_id' => 1,
                'is_main_variant' => false,
                'price' => '9990000.00',
                'sale_price' => '7290000.00',
                'created_at' => '2025-08-09 21:55:35',
                'updated_at' => '2025-08-09 21:55:35',
                'sku' => 'MFBNCBD04 B3',
                'attributes' => '{"K\\u00edch th\\u01b0\\u1edbc":"1m6","M\\u00e0u":"N\\u00e2u"}',
                'weight' => 5000,
            ),
            7 => 
            array (
                'id' => 13,
                'product_id' => 1,
                'is_main_variant' => false,
                'price' => '10990000.00',
                'sale_price' => '8290000.00',
                'created_at' => '2025-08-09 21:55:35',
                'updated_at' => '2025-08-09 21:55:35',
                'sku' => 'MFBNCBD04 B4',
                'attributes' => '{"K\\u00edch th\\u01b0\\u1edbc":"1m8","M\\u00e0u":"N\\u00e2u"}',
                'weight' => 5000,
            ),
        ));
        
        
    }
}