<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CartsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('carts')->delete();
        
        \DB::table('carts')->insert(array (
            0 => 
            array (
                'id' => 39,
                'user_id' => 1,
                'product_variant_id' => 8,
                'quantity' => 10,
                'created_at' => '2025-08-11 11:56:39',
                'updated_at' => '2025-08-11 13:46:09',
                'is_selected' => true,
            ),
        ));
        
        
    }
}