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

        view::composer('layouts.footer', function ($view) {
            $footerAccordionCategories = Category::query()
                ->whereNull('parent_id') // Lấy danh mục cha
                ->where('is_active', 1)
                ->latest() // Sắp xếp mới nhất lên trước
                ->take(4) // Giới hạn 4
                ->with(['children' => function ($query) {
                    $query->where('is_active', 1)->latest()->take(3); // Với mỗi cha, lấy 3 con mới nhất
                }])
                ->get();
            // THÊM MỚI: Logic lấy brands
            $brands = \App\Models\Brand::active()->take(6)->get();

            // Gửi cả 2 biến tới view
            $view->with('footerAccordionCategories', $footerAccordionCategories)
                ->with('brands', $brands);
        });
        if (request()->routeIs('staff.*') || request()->is('staff/*')) {
            View::replaceNamespace('admins', [
                resource_path('views/staffs/admin-bridge'),
                resource_path('views/admins'),
            ]);
        }
    }
}
