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
use App\Models\RoutePermission;
use Illuminate\Support\Facades\Blade;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if (Schema::hasTable('settings')) {
            $settings = Cache::rememberForever(
                'settings',
                fn() =>
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

        // @perm('products','create')
        Blade::if('perm', function (string $module, string $action) {
            return auth()->check() && auth()->user()->hasPermission($module, $action);
        });

        // @permroute('staff.products.create') → tra DB map (nếu không có thì fallback theo quy ước)
        Blade::if('permroute', function (string $routeName) {
            if (!auth()->check()) return false;
            $rp = RoutePermission::where('is_active', true)->where('route_name', $routeName)->first();

            if (!$rp) {
                $parts = explode('.', $routeName);
                if (count($parts) >= 3) {
                    [$area, $module, $rest] = [$parts[0], $parts[1], $parts[2]];
                    $action = match ($rest) {
                        'index', 'show'   => 'view',
                        'create', 'store' => 'create',
                        'edit', 'update'  => 'update',
                        'destroy'        => 'delete',
                        default          => null
                    };
                    if ($action) return auth()->user()->hasPermission($module, $action);
                }
                return true; // nếu không map được thì cho thấy UI (tuỳ bạn)
            }

            return auth()->user()->hasPermission($rp->module_name, $rp->action);
        });
        View::composer('staffs.*', function ($view) {
            $menu = collect();
            $user = Auth::user();
            if ($user) {
                $rolePerms   = optional($user->role)->permissions ?? collect();
                $directPerms = method_exists($user, 'permissions') ? $user->permissions : collect();
                $allPerms    = $rolePerms->concat($directPerms)->unique('id');

                $viewable = $allPerms->where('action', 'view')->groupBy('module_name')->keys();
                $map = config('staff_modules');

                foreach ($viewable as $module) {
                    if (!isset($map[$module])) continue;
                    $menu->push([
                        'label'  => $map[$module]['label'] ?? ucfirst($module),
                        'icon'   => $map[$module]['icon']  ?? 'bi-circle',
                        'route'  => $map[$module]['index'] ?? null,
                        'active' => request()->routeIs('staff.' . $module . '.*'),
                    ]);
                }
            }
            $view->with('staffMenu', $menu);
        });
    }
}
