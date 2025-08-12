<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Slide;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\Voucher;

class HomeController extends Controller
{
    public function index()
    {
        // Lấy danh sách slide đang hoạt động
        $slides = Slide::where('is_active', true)->orderBy('position')->get();

        // Lấy 8 sản phẩm nổi bật
        $featuredProducts = Product::where('is_featured', true)
            ->active()
            ->with(['variants', 'images'])
            ->latest()
            ->take(8)
            ->get();

        // Lấy 12 sản phẩm mới nhất
        $latestProducts = Product::active()
            ->with(['variants', 'images'])
            ->latest()
            ->take(12)
            ->get();

        // Lấy 6 danh mục cha đang hoạt động
        $categories = Category::active()
            ->whereNull('parent_id')
            ->orderBy('position')
            ->take(6)
            ->get();

        $specialOfferProducts = Product::whereHas('variants', function ($query) {
            $query->whereColumn('sale_price', '<', 'price');
        })->with(['variants', 'images'])->take(8)->get();
        $vouchers = Voucher::where('is_active', true)
            ->where('end_at', '>', now())
            ->orderBy('end_at', 'asc')
            ->limit(8)
            ->get();

        // Lấy 6 danh mục con
        $topCategories = Category::whereNotNull('parent_id')
            ->with(['products' => function ($query) {
                $query
                    ->with(['variants', 'images']) // preload hình ảnh thay vì firstMedia
                    ->withCount('orderItems')
                    ->orderByDesc('order_items_count')
                    ->take(8);
            }])
            ->take(6)
            ->get();



        // Gửi tất cả dữ liệu sang view index.blade.php
        return view('index', compact(
            'slides',
            'featuredProducts',
            'latestProducts',
            'categories', // danh mục cha
            'specialOfferProducts',
            'vouchers',
            'topCategories' // danh mục con + sp
        ));
    }
}
