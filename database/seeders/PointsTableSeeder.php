<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PointsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('points')->delete();
        
        
        
    }
}