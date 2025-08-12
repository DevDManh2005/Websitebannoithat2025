<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class InventoryTransactionsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('inventory_transactions')->delete();
        
        
        
    }
}