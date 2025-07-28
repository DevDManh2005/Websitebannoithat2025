<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    /**
     * Hiển thị trang danh sách sản phẩm của một danh mục.
     *
     * @param  string  $slug
     * @return \Illuminate\View\View
     */
    public function show(string $slug)
    {
        $category = Category::where('slug', $slug)->active()->firstOrFail();

        // Lấy ID các sản phẩm có trong wishlist của người dùng (nếu đã đăng nhập)
        $wishlistProductIds = Auth::check() ? Auth::user()->wishlist()->pluck('products.id')->toArray() : [];

        $products = $category->products()
            ->active()
            ->with([
                'variants' => fn($query) => $query->where('is_main_variant', true),
                'images' => fn($query) => $query->where('is_primary', true)
            ])
            ->latest()
            ->paginate(12);

        // Truyền cả ID của wishlist qua view để tối ưu việc kiểm tra
        return view('frontend.products.index', compact('category', 'products', 'wishlistProductIds'));
    }
}
