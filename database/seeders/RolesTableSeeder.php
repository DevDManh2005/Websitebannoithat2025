<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('roles')->delete();
        
        \DB::table('roles')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'admin',
                'description' => NULL,
                'created_at' => '2025-08-06 12:15:22',
                'updated_at' => '2025-08-06 12:15:22',
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'staff',
                'description' => NULL,
                'created_at' => '2025-08-06 12:15:22',
                'updated_at' => '2025-08-06 12:15:22',
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'user',
                'description' => NULL,
                'created_at' => '2025-08-06 12:15:23',
                'updated_at' => '2025-08-06 12:15:23',
            ),
        ));
        
        
    }
}