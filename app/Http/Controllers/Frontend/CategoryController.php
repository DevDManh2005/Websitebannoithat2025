<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Brand;
use App\Models\ProductVariant;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Hiển thị trang danh sách sản phẩm của một danh mục.
     */
    public function show(string $slug)
    {
        // Tìm danh mục hiện tại dựa trên slug
        $currentCategory = Category::where('slug', $slug)->firstOrFail();

        // Lấy tất cả sản phẩm thuộc danh mục này và phân trang
        $products = $currentCategory->products()
            ->active()
            ->with(['variants', 'images'])
            ->paginate(12);
        
        // --- LẤY DỮ LIỆU CHO BỘ LỌC ---
        // Lấy tất cả danh mục cha để hiển thị trong bộ lọc
        $filterCategories = Category::where('is_active', true)->whereNull('parent_id')->get();
        // Lấy tất cả thương hiệu
        $filterBrands = Brand::where('is_active', true)->get();
        // Lấy giá cao nhất để làm giới hạn cho thanh trượt giá
        $maxPrice = ProductVariant::max('price');

        // Sử dụng view products.index và truyền đầy đủ dữ liệu
        return view('frontend.products.index', [
            'products' => $products,
            'currentCategory' => $currentCategory, // Truyền danh mục hiện tại để hiển thị tên
            'categories' => $filterCategories,
            'brands' => $filterBrands,
            'max_price' => $maxPrice ?? 50000000
        ]);
    }
}
