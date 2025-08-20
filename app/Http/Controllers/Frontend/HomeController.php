<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Slide;
use App\Models\Category;
use App\Models\Voucher;
use App\Models\Blog; // <-- THÊM
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // Slides
        $slides = Slide::where('is_active', true)->orderBy('position')->get();

        // Sản phẩm nổi bật (8)
        $featuredProducts = Product::where('is_featured', true)
            ->active()
            ->with(['variants', 'images'])
            ->latest()
            ->take(8)
            ->get();

        // Sản phẩm mới (12)
        $latestProducts = Product::active()
            ->with(['variants', 'images'])
            ->latest()
            ->take(12)
            ->get();

        // Danh mục cha (6)
        $categories = Category::active()
            ->whereNull('parent_id')
            ->orderBy('position')
            ->take(6)
            ->get();

        // SP đang giảm giá (8)
        $specialOfferProducts = Product::whereHas('variants', function ($q) {
                $q->whereColumn('sale_price', '<', 'price');
            })
            ->with(['variants', 'images'])
            ->take(8)
            ->get();

        // Voucher còn hạn (8)
        $vouchers = Voucher::where('is_active', true)
            ->where('end_at', '>', now())
            ->orderBy('end_at', 'asc')
            ->limit(8)
            ->get();

        // Danh mục con (6) + SP bán chạy
        $topCategories = Category::whereNotNull('parent_id')
            ->with(['products' => function ($q) {
                $q->with(['variants', 'images'])
                  ->withCount('orderItems')
                  ->orderByDesc('order_items_count')
                  ->take(8);
            }])
            ->take(6)
            ->get();

        // 3 bài viết mới nhất
        $latestBlogs = Blog::published()
            ->orderByDesc('published_at')
            ->orderByDesc('created_at')
            ->take(3)
            ->get(['id','title','slug','excerpt','thumbnail','published_at','created_at']);

        // Trả về view bằng mảng kết hợp (KHÔNG dùng compact key=>value)
        return view('index', [
            'slides'              => $slides,
            'featuredProducts'    => $featuredProducts,
            'latestProducts'      => $latestProducts,
            'categories'          => $categories,
            'specialOfferProducts'=> $specialOfferProducts,
            'vouchers'            => $vouchers,
            'topCategories'       => $topCategories,
            'latestPosts'         => $latestBlogs, // tên biến view bạn đang dùng
        ]);
    }
}
