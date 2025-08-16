<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // --- CẤP 1: CÁC BẢNG NỀN TẢNG (KHÔNG PHỤ THUỘC) ---
            BrandsTableSeeder::class,
            CategoriesTableSeeder::class,
            CurrenciesTableSeeder::class,
            LanguagesTableSeeder::class,
            LocationsTableSeeder::class,
            PagesTableSeeder::class,
            PermissionsTableSeeder::class,
            RolesTableSeeder::class,
            SettingsTableSeeder::class,
            SlidesTableSeeder::class,
            SuppliersTableSeeder::class,
            BannersTableSeeder::class,
            VouchersTableSeeder::class,

            // --- CẤP 2: CÁC BẢNG PHỤ THUỘC CẤP 1 ---
            UsersTableSeeder::class,                // Phụ thuộc Roles
            ProductsTableSeeder::class,             // Phụ thuộc Brands, Suppliers
            
            // --- CẤP 3: CÁC BẢNG PHỤ THUỘC CẤP 2 ---
            UserProfilesTableSeeder::class,         // Phụ thuộc Users
            ProductVariantsTableSeeder::class,      // Phụ thuộc Products
            ProductImagesTableSeeder::class,        // Phụ thuộc Products
            CategoryProductTableSeeder::class,      // Phụ thuộc Categories, Products
            ProductReviewsTableSeeder::class,       // Phụ thuộc Users, Products
            WishlistsTableSeeder::class,            // Phụ thuộc Users, Products
            CartsTableSeeder::class,                // Phụ thuộc Users, ProductVariants
            InventoriesTableSeeder::class,          // Phụ thuộc Products, ProductVariants, Locations
            OrdersTableSeeder::class,               // Phụ thuộc Users
            PointsTableSeeder::class,               // Phụ thuộc Users
            BlogsTableSeeder::class,                // Phụ thuộc Users
            SupportTicketsTableSeeder::class,       // Phụ thuộc Users
            NotificationsTableSeeder::class,        // Phụ thuộc Users
            AuditLogsTableSeeder::class,            // Phụ thuộc Users
            PermissionUserTableSeeder::class,       // Phụ thuộc Permissions, Users
            RolePermissionTableSeeder::class,       // Phụ thuộc Roles, Permissions
            
            // --- CẤP 4: CÁC BẢNG PHỤ THUỘC CẤP 3 ---
            OrderItemsTableSeeder::class,           // Phụ thuộc Orders, ProductVariants
            ShipmentsTableSeeder::class,            // Phụ thuộc Orders
            PaymentsTableSeeder::class,             // Phụ thuộc Orders
            BlogCommentsTableSeeder::class,         // Phụ thuộc Blogs, Users
            SupportRepliesTableSeeder::class,       // Phụ thuộc SupportTickets, Users
            InventoryTransactionsTableSeeder::class,// Phụ thuộc Inventories, Users

            // --- CÁC BẢNG HỆ THỐNG (thường không có khóa ngoại quan trọng) ---
            FailedJobsTableSeeder::class,
            MigrationsTableSeeder::class,
            OtpsTableSeeder::class,
            PasswordResetTokensTableSeeder::class,
            PersonalAccessTokensTableSeeder::class,
            
        ]);
        $this->call(\Database\Seeders\Patches\DashboardPermissionPatchSeeder::class);

    }
    
}