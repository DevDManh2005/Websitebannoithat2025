<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        // module => actions
        $defs = [
            'dashboard'       => ['view'],

            'orders'          => ['view','update','change_status','ready_to_ship','cod_paid'],
            'products'        => ['view','create','update','delete'],
            'categories'      => ['view','create','update','delete'],
            'brands'          => ['view','create','update','delete'],
            'suppliers'       => ['view','create','update','delete'],

            'inventories'     => ['view','update','delete','create'], // gom về nhóm kho
            'reviews'         => ['view','moderate','delete'],
            'vouchers'        => ['view','create','update','delete'],
            'slides'          => ['view','create','update','delete'],
            'blogs'           => ['view','create','update','delete','publish'],
            'blog-categories' => ['view','create','update','delete'],

            // các mục mở rộng để “full” lựa chọn
            'banners'         => ['view','create','update','delete'],
            'pages'           => ['view','create','update','delete'],
            'uploads'         => ['update'],

            // quản trị người dùng (nếu cấp cho staff)
            'settings'        => ['view','update'],
            'users'           => ['view','update','ban'],
            'staffs'          => ['view','create','update','delete','grant_permission'],
        ];

        $rows = [];
        foreach ($defs as $module => $actions) {
            foreach ($actions as $action) {
                $rows[] = [
                    'module_name' => $module,
                    'action'      => $action,
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ];
            }
        }

        DB::table('permissions')->upsert(
            $rows,
            ['module_name','action'],
            ['updated_at']
        );
    }
}
