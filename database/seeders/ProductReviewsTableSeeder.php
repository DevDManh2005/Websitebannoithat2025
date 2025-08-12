<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ProductReviewsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('product_reviews')->delete();
        
        \DB::table('product_reviews')->insert(array (
            0 => 
            array (
                'id' => 2,
                'user_id' => 1,
                'product_id' => 1,
                'rating' => 5,
                'review' => 'ffafass',
                'image' => 'reviews/0XbQiwrJniLdYd8VLI551iiMJhMedVFYyCjaMsod.jpg',
                'created_at' => '2025-08-06 16:59:55',
                'updated_at' => '2025-08-11 10:06:06',
                'status' => 'approved',
            ),
        ));
        
        
    }
}