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
    public function register(): void
    {
        //
    }

   public function boot(): void
{
    if (Schema::hasTable('settings')) {
        $settings = Cache::rememberForever('settings', fn () =>
            Setting::pluck('value', 'key')->all()
        );
        View::share('settings', $settings);
    }

    View::composer([
        // tất cả header + nav partials dùng chung dữ liệu
        'layouts.partials.header-home',
        'layouts.partials.header-internal',
        'layouts.partials._nav_actions',   // <-- THÊM DÒNG NÀY
        'layouts.partials._nav_links',     // <-- (khuyến nghị)
    ], function ($view) {

        // đệ quy children
        $withChildren = function ($q) use (&$withChildren) {
            $q->where('is_active', true)
              ->orderBy('position')
              ->with(['children' => $withChildren]);
        };

        $sharedCategories = Cache::remember('shared_categories_tree', now()->addDay(), function () use ($withChildren) {
            return Category::whereNull('parent_id')
                ->where('is_active', true)
                ->orderBy('position')
                ->with(['children' => $withChildren])
                ->get();
        });

        $cartItemCount = 0;
        $wishlistItemCount = 0;

        if (Auth::check()) {
            $user = Auth::user();
            // tuỳ bạn muốn count item hay sum quantity
            $cartItemCount = Cart::where('user_id', $user->id)->count();
            $wishlistItemCount = $user->wishlist()->count();
        }

        $view->with('sharedCategories', $sharedCategories)
             ->with('sharedCategoriesTree', $sharedCategories) // alias
             ->with('sharedCartItemCount', $cartItemCount)
             ->with('sharedWishlistItemCount', $wishlistItemCount);
    });
}
}