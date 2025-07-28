<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Category;
use App\Models\Cart;

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
        View::composer('layouts.header', function ($view) {
            $sharedCategories = Category::whereNull('parent_id')
                                  ->with(['children' => fn($q) => $q->active()->orderBy('position')])
                                  ->active()
                                  ->orderBy('position')
                                  ->get();
            
            $cartItemCount = 0;
            $wishlistItemCount = 0;

            if (Auth::check()) {
                $user = Auth::user();
                $cartItemCount = Cart::where('user_id', $user->id)->count();
                // Lấy số lượng sản phẩm trong wishlist
                $wishlistItemCount = $user->wishlist()->count();
            }
            
            $view->with('sharedCategories', $sharedCategories)
                 ->with('sharedCartItemCount', $cartItemCount)
                 ->with('sharedWishlistItemCount', $wishlistItemCount); // <-- Thêm biến mới
        });
    }
}
