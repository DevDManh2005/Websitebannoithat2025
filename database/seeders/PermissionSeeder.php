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

            'inventories'     => ['view','update','delete','create'],
            'reviews'         => ['view','moderate','delete'],
            'vouchers'        => ['view','create','update','delete'],
            'slides'          => ['view','create','update','delete'],
            'blogs'           => ['view','create','update','delete','publish'],
            'blog-categories' => ['view','create','update','delete'],

            // 'banners'         => ['view','create','update','delete'],
            // 'pages'           => ['view','create','update','delete'],
            'uploads'         => ['update'],

            // 'settings'        => ['view','update'],
            // 'users'           => ['view','update','ban'],
            // 'staffs'          => ['view','create','update','delete','grant_permission'],

            // ➜ BỔ SUNG CHO SUPPORT
            'support-tickets' => ['view','update','delete','reply'],
        ];

        // Upsert permissions
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

        // (Khuyên dùng) Upsert luôn mapping route_permissions cho ADMIN
        $routes = [
            ['route_name'=>'admin.support-tickets.index',        'module_name'=>'support-tickets','action'=>'view','area'=>'admin','is_active'=>1],
            ['route_name'=>'admin.support-tickets.show',         'module_name'=>'support-tickets','action'=>'view','area'=>'admin','is_active'=>1],
            ['route_name'=>'admin.support-tickets.updateStatus', 'module_name'=>'support-tickets','action'=>'update','area'=>'admin','is_active'=>1],
            ['route_name'=>'admin.support-replies.store',        'module_name'=>'support-tickets','action'=>'reply','area'=>'admin','is_active'=>1],
            // Nếu sau này có xóa ticket:
            // ['route_name'=>'admin.support-tickets.destroy',   'module_name'=>'support-tickets','action'=>'delete','area'=>'admin','is_active'=>1],
        ];

        $routeRows = [];
        foreach ($routes as $r) {
            $routeRows[] = $r + ['created_at'=>$now,'updated_at'=>$now];
        }
        DB::table('route_permissions')->upsert(
            $routeRows,
            ['route_name'], // bảng có unique theo route_name
            ['module_name','action','area','is_active','updated_at']
        );
    }
}
