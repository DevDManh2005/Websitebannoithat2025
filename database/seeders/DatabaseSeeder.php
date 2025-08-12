<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
   public function run()
{
    $this->call([
        RoleSeeder::class,
        AdminStaffSeeder::class,
        // ... các seeder khác nếu có
        
    ]);
    $this->call(AuditLogsTableSeeder::class);
        $this->call(BannersTableSeeder::class);
        $this->call(BlogCommentsTableSeeder::class);
        $this->call(BlogsTableSeeder::class);
        $this->call(BrandsTableSeeder::class);
        $this->call(CartsTableSeeder::class);
        $this->call(CategoriesTableSeeder::class);
        $this->call(CategoryProductTableSeeder::class);
        $this->call(CurrenciesTableSeeder::class);
        $this->call(FailedJobsTableSeeder::class);
        $this->call(InventoriesTableSeeder::class);
        $this->call(InventoryTransactionsTableSeeder::class);
        $this->call(LanguagesTableSeeder::class);
        $this->call(LocationsTableSeeder::class);
        $this->call(MigrationsTableSeeder::class);
        $this->call(NotificationsTableSeeder::class);
        $this->call(OrderItemsTableSeeder::class);
        $this->call(OrdersTableSeeder::class);
        $this->call(OtpsTableSeeder::class);
        $this->call(PagesTableSeeder::class);
        $this->call(PasswordResetTokensTableSeeder::class);
        $this->call(PaymentsTableSeeder::class);
        $this->call(PermissionUserTableSeeder::class);
        $this->call(PermissionsTableSeeder::class);
        $this->call(PersonalAccessTokensTableSeeder::class);
        $this->call(PointsTableSeeder::class);
        $this->call(ProductImagesTableSeeder::class);
        $this->call(ProductReviewsTableSeeder::class);
        $this->call(ProductVariantsTableSeeder::class);
        $this->call(ProductsTableSeeder::class);
        $this->call(RolePermissionTableSeeder::class);
        $this->call(RolesTableSeeder::class);
        $this->call(SettingsTableSeeder::class);
        $this->call(ShipmentsTableSeeder::class);
        $this->call(SlidesTableSeeder::class);
        $this->call(SuppliersTableSeeder::class);
        $this->call(SupportRepliesTableSeeder::class);
        $this->call(SupportTicketsTableSeeder::class);
        $this->call(UserProfilesTableSeeder::class);
        $this->call(UsersTableSeeder::class);
        $this->call(VouchersTableSeeder::class);
        $this->call(WishlistsTableSeeder::class);
    }

}
