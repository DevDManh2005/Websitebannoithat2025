<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class BlogCommentsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('blog_comments')->delete();
        
        
        
    }
}