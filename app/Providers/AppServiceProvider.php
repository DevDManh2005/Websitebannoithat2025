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
use App\Models\Brand;
use App\Models\RoutePermission;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route as Rt;
use Illuminate\Pagination\Paginator;
// Thêm model SupportTicket
use App\Models\SupportTicket;

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
                fn() => Setting::pluck('value', 'key')->all()
            );
            View::share('settings', $settings);
        }

        Paginator::defaultView('pagination::bootstrap-5');
        Paginator::defaultSimpleView('pagination::simple-bootstrap-5');

        // --- MỘT VIEW COMPOSER DUY NHẤT CHO TOÀN BỘ HEADER ---
        View::composer([
            'layouts.partials.header-home',
            'layouts.partials.header-internal',
            'layouts.partials._nav_actions',
            'layouts.partials._nav_links',
        ], function ($view) {
            // --- Shared Categories ---
            $sharedCategories = Cache::remember('shared_categories_tree', now()->addDay(), function () {
                $withChildren = function ($q) use (&$withChildren) {
                    $q->where('is_active', true)
                        ->orderBy('position')
                        ->with(['children' => $withChildren]);
                };
                return Category::whereNull('parent_id')
                    ->where('is_active', true)
                    ->orderBy('position')
                    ->with(['children' => $withChildren])
                    ->get();
            });

            // --- User specific data ---
            $cartItemCount = 0;
            $wishlistItemCount = 0;
            $supportOpenTickets = 0; // Đổi tên biến cho rõ nghĩa

            if (Auth::check()) {
                $user = Auth::user();
                $cartItemCount = Cart::where('user_id', $user->id)->count();
                $wishlistItemCount = $user->wishlist()->count();

                // THAY ĐỔI LOGIC: Đếm số ticket đang mở thay vì tin nhắn chưa đọc
                if (Schema::hasTable('support_tickets')) {
                    $supportOpenTickets = SupportTicket::query()
                        ->where('user_id', $user->id)
                        ->where('status', 'open') // Giả định có cột status
                        ->count();
                }
            }

            // Gửi tất cả các biến qua view
            $view->with('sharedCategories', $sharedCategories)
                ->with('sharedCategoriesTree', $sharedCategories) // alias
                ->with('sharedCartItemCount', $cartItemCount)
                ->with('sharedWishlistItemCount', $wishlistItemCount)
                ->with('supportOpenTickets', $supportOpenTickets); // Gửi biến mới
        });

        // --- Các logic khác giữ nguyên ---
        Blade::if('perm', function (string $module, string $action) {
            return auth()->check() && auth()->user()->hasPermission($module, $action);
        });

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
                        'destroy'         => 'delete',
                        default           => null
                    };
                    if ($action) return auth()->user()->hasPermission($module, $action);
                }
                return true;
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
                $map = config('staff_modules', []);

                foreach ($viewable as $module) {
                    if (!isset($map[$module])) continue;
                    $routeName = $map[$module]['index'] ?? null;
                    $menu->push([
                        'label'  => $map[$module]['label'] ?? ucfirst($module),
                        'icon'   => $map[$module]['icon']  ?? 'ri-circle-line',
                        'route'  => Rt::has($routeName) ? $routeName : null,
                        'active' => request()->routeIs('staff.' . $module . '.*'),
                    ]);
                }
            }

            $view->with('staffMenu', $menu);
        });

        View::composer('frontend.components.blog-category-menu', function ($view) {
            $blogCategories = \App\Models\BlogCategory::query()
                ->where('is_active', 1)
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get();
            $view->with('blogCategories', $blogCategories);
        });

        View::composer('layouts.footer', function ($view) {

            // 1. Lấy dữ liệu cho slider đối tác
            // Dòng này yêu cầu bạn phải có scope "active()" trong Model Brand
            $brands = Brand::query()
                ->where('is_active', 1) // Hoặc dùng scope: Brand::active()
                ->take(8)
                ->get();

            // 2. Lấy dữ liệu cho accordion danh mục sản phẩm ở footer
            $footerAccordionCategories = Category::query()
                ->where('is_active', true)
                ->whereNull('parent_id') // Chỉ lấy các danh mục cha
                ->with(['children' => function ($query) {
                    $query->where('is_active', true); // Kèm theo các danh mục con đang hoạt động
                }])
                ->get();
            
            // 3. Lấy dữ liệu cài đặt chung của website
            // Cách này hiệu quả để lấy tất cả settings và biến nó thành mảng key => value
            $settings = Setting::all()->pluck('value', 'key')->all();

            // 4. Gửi tất cả dữ liệu sang view footer
            $view->with([
                'brandsForFooter' => $brands,
                'footerAccordionCategories' => $footerAccordionCategories,
                'settings' => $settings,
            ]);
        });
        if (request()->routeIs('staff.*') || request()->is('staff/*')) {
            View::replaceNamespace('admins', [
                resource_path('views/staffs/admin-bridge'),
                resource_path('views/admins'),
            ]);
        }
    }
}
