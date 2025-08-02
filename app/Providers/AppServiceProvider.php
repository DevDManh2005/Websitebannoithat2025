<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use App\Models\Category;
use App\Models\Cart;
use App\Models\Setting;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Sử dụng try-catch để tránh lỗi khi chạy migrate lần đầu tiên khi bảng chưa tồn tại
        try {
            // Lấy tất cả cài đặt, cache lại vĩnh viễn, và chia sẻ cho TẤT CẢ các view
            // Cache sẽ tự động được xóa khi bạn cập nhật setting trong SettingController
            $settings = Cache::rememberForever('settings', function () {
                return Setting::pluck('value', 'key')->all();
            });
            View::share('settings', $settings);

            // View Composer này chỉ chạy cho các view được chỉ định (layouts.header, layouts.app)
            // để lấy dữ liệu riêng cho header như danh mục, số lượng giỏ hàng...
            View::composer(['layouts.header', 'layouts.app'], function ($view) {
                $sharedCategories = Cache::remember('shared_categories', now()->addHours(24), function () {
                    return Category::whereNull('parent_id')
                                    ->with(['children' => fn($q) => $q->active()->orderBy('position')])
                                    ->active()
                                    ->orderBy('position')
                                    ->get();
                });
                
                $cartItemCount = 0;
                $wishlistItemCount = 0;

                if (Auth::check()) {
                    $user = Auth::user();
                    $cartItemCount = Cart::where('user_id', $user->id)->count();
                    $wishlistItemCount = $user->wishlist()->count();
                }
                
                $view->with('sharedCategories', $sharedCategories)
                     ->with('sharedCartItemCount', $cartItemCount)
                     ->with('sharedWishlistItemCount', $wishlistItemCount);
            });
        } catch (\Exception $e) {
            // Ghi log lỗi nếu cần, nhưng không làm sập ứng dụng
            // Log::error("Could not boot AppServiceProvider: " . $e->getMessage());
        }
    }
}