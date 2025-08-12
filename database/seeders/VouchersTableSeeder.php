<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class VouchersTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('vouchers')->delete();
        
        \DB::table('vouchers')->insert(array (
            0 => 
            array (
                'id' => 2,
                'code' => 'XDLDMXD',
                'type' => 'percent',
                'value' => '10.00',
                'min_order_amount' => '200000.00',
                'usage_limit' => 20,
                'used_count' => 7,
                'start_at' => '2025-08-07 01:52:00',
                'end_at' => '2025-09-07 01:52:00',
                'is_active' => true,
                'created_at' => '2025-08-07 01:52:41',
                'updated_at' => '2025-08-11 09:03:34',
            ),
            1 => 
            array (
                'id' => 4,
                'code' => 'TEST1',
                'type' => 'percent',
                'value' => '1000.00',
                'min_order_amount' => '500000.00',
                'usage_limit' => 10,
                'used_count' => 0,
                'start_at' => '2025-08-11 14:31:00',
                'end_at' => '2025-08-31 14:31:00',
                'is_active' => true,
                'created_at' => '2025-08-11 14:31:59',
                'updated_at' => '2025-08-11 14:33:23',
            ),
        ));
        
        
    }
}