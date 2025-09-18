<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class RoutePermissionStaffSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $rows = [
            // DASHBOARD
            ['route_name' => 'staff.dashboard', 'module_name' => 'dashboard', 'action' => 'view'],

            // ORDERS
            ['route_name' => 'staff.orders.index',             'module_name' => 'orders', 'action' => 'view'],
            ['route_name' => 'staff.orders.show',              'module_name' => 'orders', 'action' => 'view'],
            ['route_name' => 'staff.orders.updateStatus',      'module_name' => 'orders', 'action' => 'update'],
            ['route_name' => 'staff.orders.updateShippingInfo','module_name' => 'orders', 'action' => 'update'],
            ['route_name' => 'staff.orders.readyToShip',       'module_name' => 'orders', 'action' => 'ready_to_ship'],
            ['route_name' => 'staff.orders.codPaid',           'module_name' => 'orders', 'action' => 'cod_paid'],
            ['route_name' => 'staff.notifications.new-orders', 'module_name' => 'orders', 'action' => 'view'],

            // REVIEWS
            ['route_name' => 'staff.reviews.index',            'module_name' => 'reviews', 'action' => 'view'],
            ['route_name' => 'staff.reviews.toggleStatus',     'module_name' => 'reviews', 'action' => 'moderate'],
            ['route_name' => 'staff.reviews.destroy',          'module_name' => 'reviews', 'action' => 'delete'],

            // PRODUCTS
            ['route_name' => 'staff.products.index',           'module_name' => 'products', 'action' => 'view'],
            ['route_name' => 'staff.products.create',          'module_name' => 'products', 'action' => 'create'],
            ['route_name' => 'staff.products.store',           'module_name' => 'products', 'action' => 'create'],
            ['route_name' => 'staff.products.show',            'module_name' => 'products', 'action' => 'view'],
            ['route_name' => 'staff.products.edit',            'module_name' => 'products', 'action' => 'update'],
            ['route_name' => 'staff.products.update',          'module_name' => 'products', 'action' => 'update'],
            ['route_name' => 'staff.products.destroy',         'module_name' => 'products', 'action' => 'delete'],
            ['route_name' => 'staff.products.variants.inventory','module_name' => 'inventories','action' => 'view'],

            // CATEGORIES
            ['route_name' => 'staff.categories.index',         'module_name' => 'categories', 'action' => 'view'],
            ['route_name' => 'staff.categories.create',        'module_name' => 'categories', 'action' => 'create'],
            ['route_name' => 'staff.categories.store',         'module_name' => 'categories', 'action' => 'create'],
            ['route_name' => 'staff.categories.show',          'module_name' => 'categories', 'action' => 'view'],
            ['route_name' => 'staff.categories.edit',          'module_name' => 'categories', 'action' => 'update'],
            ['route_name' => 'staff.categories.update',        'module_name' => 'categories', 'action' => 'update'],
            ['route_name' => 'staff.categories.destroy',       'module_name' => 'categories', 'action' => 'delete'],

            // BRANDS
            ['route_name' => 'staff.brands.index',             'module_name' => 'brands', 'action' => 'view'],
            ['route_name' => 'staff.brands.create',            'module_name' => 'brands', 'action' => 'create'],
            ['route_name' => 'staff.brands.store',             'module_name' => 'brands', 'action' => 'create'],
            ['route_name' => 'staff.brands.show',              'module_name' => 'brands', 'action' => 'view'],
            ['route_name' => 'staff.brands.edit',              'module_name' => 'brands', 'action' => 'update'],
            ['route_name' => 'staff.brands.update',            'module_name' => 'brands', 'action' => 'update'],
            ['route_name' => 'staff.brands.destroy',           'module_name' => 'brands', 'action' => 'delete'],

            // SUPPLIERS
            ['route_name' => 'staff.suppliers.index',          'module_name' => 'suppliers', 'action' => 'view'],
            ['route_name' => 'staff.suppliers.create',         'module_name' => 'suppliers', 'action' => 'create'],
            ['route_name' => 'staff.suppliers.store',          'module_name' => 'suppliers', 'action' => 'create'],
            ['route_name' => 'staff.suppliers.show',           'module_name' => 'suppliers', 'action' => 'view'],
            ['route_name' => 'staff.suppliers.edit',           'module_name' => 'suppliers', 'action' => 'update'],
            ['route_name' => 'staff.suppliers.update',         'module_name' => 'suppliers', 'action' => 'update'],
            ['route_name' => 'staff.suppliers.destroy',        'module_name' => 'suppliers', 'action' => 'delete'],

            // INVENTORIES
            ['route_name' => 'staff.inventories.index',        'module_name' => 'inventories', 'action' => 'view'],
            ['route_name' => 'staff.inventories.create',       'module_name' => 'inventories', 'action' => 'update'],
            ['route_name' => 'staff.inventories.store',        'module_name' => 'inventories', 'action' => 'update'],
            ['route_name' => 'staff.inventories.show',         'module_name' => 'inventories', 'action' => 'view'],
            ['route_name' => 'staff.inventories.edit',         'module_name' => 'inventories', 'action' => 'update'],
            ['route_name' => 'staff.inventories.update',       'module_name' => 'inventories', 'action' => 'update'],
            ['route_name' => 'staff.inventories.destroy',      'module_name' => 'inventories', 'action' => 'update'],

            // VOUCHERS
            ['route_name' => 'staff.vouchers.index',           'module_name' => 'vouchers', 'action' => 'view'],
            ['route_name' => 'staff.vouchers.create',          'module_name' => 'vouchers', 'action' => 'create'],
            ['route_name' => 'staff.vouchers.store',           'module_name' => 'vouchers', 'action' => 'create'],
            ['route_name' => 'staff.vouchers.show',            'module_name' => 'vouchers', 'action' => 'view'],
            ['route_name' => 'staff.vouchers.edit',            'module_name' => 'vouchers', 'action' => 'update'],
            ['route_name' => 'staff.vouchers.update',          'module_name' => 'vouchers', 'action' => 'update'],
            ['route_name' => 'staff.vouchers.destroy',         'module_name' => 'vouchers', 'action' => 'delete'],

            // SLIDES
            ['route_name' => 'staff.slides.index',             'module_name' => 'slides', 'action' => 'view'],
            ['route_name' => 'staff.slides.create',            'module_name' => 'slides', 'action' => 'create'],
            ['route_name' => 'staff.slides.store',             'module_name' => 'slides', 'action' => 'create'],
            ['route_name' => 'staff.slides.show',              'module_name' => 'slides', 'action' => 'view'],
            ['route_name' => 'staff.slides.edit',              'module_name' => 'slides', 'action' => 'update'],
            ['route_name' => 'staff.slides.update',            'module_name' => 'slides', 'action' => 'update'],
            ['route_name' => 'staff.slides.destroy',           'module_name' => 'slides', 'action' => 'delete'],

            // BLOG CATEGORIES
            ['route_name' => 'staff.blog-categories.index',    'module_name' => 'blog-categories', 'action' => 'view'],
            ['route_name' => 'staff.blog-categories.create',   'module_name' => 'blog-categories', 'action' => 'create'],
            ['route_name' => 'staff.blog-categories.store',    'module_name' => 'blog-categories', 'action' => 'create'],
            ['route_name' => 'staff.blog-categories.edit',     'module_name' => 'blog-categories', 'action' => 'update'],
            ['route_name' => 'staff.blog-categories.update',   'module_name' => 'blog-categories', 'action' => 'update'],
            ['route_name' => 'staff.blog-categories.destroy',  'module_name' => 'blog-categories', 'action' => 'delete'],

            // BLOGS + upload
            ['route_name' => 'staff.blogs.index',              'module_name' => 'blogs', 'action' => 'view'],
            ['route_name' => 'staff.blogs.create',             'module_name' => 'blogs', 'action' => 'create'],
            ['route_name' => 'staff.blogs.store',              'module_name' => 'blogs', 'action' => 'create'],
            ['route_name' => 'staff.blogs.edit',               'module_name' => 'blogs', 'action' => 'update'],
            ['route_name' => 'staff.blogs.update',             'module_name' => 'blogs', 'action' => 'update'],
            ['route_name' => 'staff.blogs.destroy',            'module_name' => 'blogs', 'action' => 'delete'],
            ['route_name' => 'staff.uploads.ckeditor',         'module_name' => 'uploads', 'action' => 'update'],
        ];

        foreach ($rows as &$r) {
            $r += ['area' => 'staff', 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now];
        }

        DB::table('route_permissions')->upsert(
            $rows, ['route_name'], ['module_name','action','area','is_active','updated_at']
        );
    }
}
