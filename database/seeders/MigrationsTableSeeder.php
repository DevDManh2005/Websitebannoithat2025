<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class MigrationsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('migrations')->delete();
        
        \DB::table('migrations')->insert(array (
            0 => 
            array (
                'id' => 1,
                'migration' => '2014_10_12_100000_create_password_reset_tokens_table',
                'batch' => 1,
            ),
            1 => 
            array (
                'id' => 2,
                'migration' => '2019_08_19_000000_create_failed_jobs_table',
                'batch' => 1,
            ),
            2 => 
            array (
                'id' => 3,
                'migration' => '2019_12_14_000001_create_personal_access_tokens_table',
                'batch' => 1,
            ),
            3 => 
            array (
                'id' => 4,
                'migration' => '2025_07_21_101438_create_roles_table',
                'batch' => 1,
            ),
            4 => 
            array (
                'id' => 5,
                'migration' => '2025_07_21_101451_create_permissions_table',
                'batch' => 1,
            ),
            5 => 
            array (
                'id' => 6,
                'migration' => '2025_07_21_101501_create_role_permission_table',
                'batch' => 1,
            ),
            6 => 
            array (
                'id' => 7,
                'migration' => '2025_07_21_101510_create_users_table',
                'batch' => 1,
            ),
            7 => 
            array (
                'id' => 8,
                'migration' => '2025_07_21_101519_create_user_profiles_table',
                'batch' => 1,
            ),
            8 => 
            array (
                'id' => 9,
                'migration' => '2025_07_21_101642_create_settings_table',
                'batch' => 1,
            ),
            9 => 
            array (
                'id' => 10,
                'migration' => '2025_07_21_101651_create_languages_table',
                'batch' => 1,
            ),
            10 => 
            array (
                'id' => 11,
                'migration' => '2025_07_21_101701_create_currencies_table',
                'batch' => 1,
            ),
            11 => 
            array (
                'id' => 12,
                'migration' => '2025_07_21_101730_create_categories_table',
                'batch' => 1,
            ),
            12 => 
            array (
                'id' => 13,
                'migration' => '2025_07_21_101742_create_brands_table',
                'batch' => 1,
            ),
            13 => 
            array (
                'id' => 14,
                'migration' => '2025_07_21_101756_create_suppliers_table',
                'batch' => 1,
            ),
            14 => 
            array (
                'id' => 15,
                'migration' => '2025_07_21_101805_create_products_table',
                'batch' => 1,
            ),
            15 => 
            array (
                'id' => 16,
                'migration' => '2025_07_21_101835_create_product_variants_table',
                'batch' => 1,
            ),
            16 => 
            array (
                'id' => 17,
                'migration' => '2025_07_21_101845_create_product_images_table',
                'batch' => 1,
            ),
            17 => 
            array (
                'id' => 18,
                'migration' => '2025_07_21_101854_create_wishlists_table',
                'batch' => 1,
            ),
            18 => 
            array (
                'id' => 19,
                'migration' => '2025_07_21_101902_create_product_reviews_table',
                'batch' => 1,
            ),
            19 => 
            array (
                'id' => 20,
                'migration' => '2025_07_21_101915_create_carts_table',
                'batch' => 1,
            ),
            20 => 
            array (
                'id' => 21,
                'migration' => '2025_07_21_101942_create_orders_table',
                'batch' => 1,
            ),
            21 => 
            array (
                'id' => 22,
                'migration' => '2025_07_21_101952_create_order_items_table',
                'batch' => 1,
            ),
            22 => 
            array (
                'id' => 23,
                'migration' => '2025_07_21_102006_create_payments_table',
                'batch' => 1,
            ),
            23 => 
            array (
                'id' => 24,
                'migration' => '2025_07_21_102017_create_shipments_table',
                'batch' => 1,
            ),
            24 => 
            array (
                'id' => 25,
                'migration' => '2025_07_21_102029_create_vouchers_table',
                'batch' => 1,
            ),
            25 => 
            array (
                'id' => 26,
                'migration' => '2025_07_21_102041_create_points_table',
                'batch' => 1,
            ),
            26 => 
            array (
                'id' => 27,
                'migration' => '2025_07_21_102118_create_support_tickets_table',
                'batch' => 1,
            ),
            27 => 
            array (
                'id' => 28,
                'migration' => '2025_07_21_102131_create_support_replies_table',
                'batch' => 1,
            ),
            28 => 
            array (
                'id' => 29,
                'migration' => '2025_07_21_102143_create_banners_table',
                'batch' => 1,
            ),
            29 => 
            array (
                'id' => 30,
                'migration' => '2025_07_21_102157_create_pages_table',
                'batch' => 1,
            ),
            30 => 
            array (
                'id' => 31,
                'migration' => '2025_07_21_102206_create_blogs_table',
                'batch' => 1,
            ),
            31 => 
            array (
                'id' => 32,
                'migration' => '2025_07_21_102217_create_blog_comments_table',
                'batch' => 1,
            ),
            32 => 
            array (
                'id' => 33,
                'migration' => '2025_07_21_102226_create_notifications_table',
                'batch' => 1,
            ),
            33 => 
            array (
                'id' => 34,
                'migration' => '2025_07_21_102236_create_audit_logs_table',
                'batch' => 1,
            ),
            34 => 
            array (
                'id' => 35,
                'migration' => '2025_07_21_102525_create_otps_table',
                'batch' => 1,
            ),
            35 => 
            array (
                'id' => 36,
                'migration' => '2025_07_21_141458_create_permission_user_table',
                'batch' => 1,
            ),
            36 => 
            array (
                'id' => 37,
                'migration' => '2025_07_22_064542_add_is_active_to_users_table',
                'batch' => 1,
            ),
            37 => 
            array (
                'id' => 38,
                'migration' => '2025_07_22_073752_add_location_to_user_profiles_table',
                'batch' => 1,
            ),
            38 => 
            array (
                'id' => 39,
                'migration' => '2025_07_22_093025_add_location_names_to_user_profiles',
                'batch' => 1,
            ),
            39 => 
            array (
                'id' => 40,
                'migration' => '2025_07_23_054408_create_locations_table',
                'batch' => 1,
            ),
            40 => 
            array (
                'id' => 41,
                'migration' => '2025_07_23_054409_create_inventories_table',
                'batch' => 1,
            ),
            41 => 
            array (
                'id' => 42,
                'migration' => '2025_07_23_054422_create_inventory_transactions_table',
                'batch' => 1,
            ),
            42 => 
            array (
                'id' => 43,
                'migration' => '2025_07_23_072212_drop_price_and_sale_price_from_products_table',
                'batch' => 1,
            ),
            43 => 
            array (
                'id' => 44,
                'migration' => '2025_07_24_013546_update_product_variants_table_for_attributes',
                'batch' => 1,
            ),
            44 => 
            array (
                'id' => 45,
                'migration' => '2025_07_24_184748_change_location_and_user_profile_address_ids_to_string',
                'batch' => 1,
            ),
            45 => 
            array (
                'id' => 46,
                'migration' => '2025_07_24_191642_add_new_order_statuses_to_orders_table',
                'batch' => 1,
            ),
            46 => 
            array (
                'id' => 47,
                'migration' => '2025_07_29_072850_add_payment_method_to_orders_table',
                'batch' => 1,
            ),
            47 => 
            array (
                'id' => 48,
                'migration' => '2025_07_29_074705_modify_status_enum_in_orders_table',
                'batch' => 1,
            ),
            48 => 
            array (
                'id' => 49,
                'migration' => '2025_07_30_100927_add_weight_to_product_variants_table',
                'batch' => 1,
            ),
            49 => 
            array (
                'id' => 50,
                'migration' => '2025_07_30_134525_add_address_ids_to_shipments_table',
                'batch' => 1,
            ),
            50 => 
            array (
                'id' => 51,
                'migration' => '2025_07_30_155512_add_status_to_product_reviews_table',
                'batch' => 1,
            ),
            51 => 
            array (
                'id' => 52,
                'migration' => '2025_07_31_181956_add_received_status_to_orders_table',
                'batch' => 1,
            ),
            52 => 
            array (
                'id' => 53,
                'migration' => '2025_08_01_142202_create_slides_table',
                'batch' => 1,
            ),
            53 => 
            array (
                'id' => 54,
                'migration' => '2025_08_01_143245_add_image_to_categories_table',
                'batch' => 1,
            ),
            54 => 
            array (
                'id' => 55,
                'migration' => '2025_08_01_143321_create_category_product_table',
                'batch' => 1,
            ),
            55 => 
            array (
                'id' => 56,
                'migration' => '2025_08_01_143340_remove_category_id_from_products_table',
                'batch' => 1,
            ),
            56 => 
            array (
                'id' => 57,
                'migration' => '2025_08_01_190944_simplify_slides_table',
                'batch' => 1,
            ),
            57 => 
            array (
                'id' => 58,
                'migration' => '2025_08_06_134122_update_order_status_constraint',
                'batch' => 2,
            ),
            58 => 
            array (
                'id' => 59,
                'migration' => '2025_08_07_092708_add_total_purchased_to_products_table',
                'batch' => 3,
            ),
            59 => 
            array (
                'id' => 60,
                'migration' => '2025_08_09_201124_add_is_selected_to_carts_table',
                'batch' => 4,
            ),
            60 => 
            array (
                'id' => 61,
                'migration' => '2025_08_10_014810_add_payment_flags_to_orders',
                'batch' => 5,
            ),
        ));
        
        
    }
}