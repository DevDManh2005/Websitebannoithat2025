<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\ProductVariant;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Hiển thị trang danh sách tất cả sản phẩm với bộ lọc.
     */
    public function index(Request $request)
    {
        $query = Product::query()->active()->with(['variants', 'images', 'categories']);

        // Lọc theo danh mục
        if ($request->filled('categories')) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->whereIn('categories.id', $request->categories);
            });
        }

        // Lọc theo thương hiệu
        if ($request->filled('brands')) {
            $query->whereIn('brand_id', $request->brands);
        }

        // Lọc theo giá (lấy giá tối đa)
        if ($request->filled('price_max')) {
            $query->whereHas('variants', function ($q) use ($request) {
                $q->where('price', '<=', $request->price_max);
            });
        }

        // Sắp xếp sản phẩm
        if ($request->filled('sort')) {
            // Lưu ý: Sắp xếp theo giá cần join bảng, sẽ phức tạp hơn.
            // Tạm thời sắp xếp theo ngày tạo mới nhất.
            $query->latest();
        } else {
            $query->latest();
        }

        $products = $query->paginate(12)->withQueryString();

        // Lấy dữ liệu cho bộ lọc
        $filterCategories = Category::where('is_active', true)->whereNull('parent_id')->get();
        $filterBrands = Brand::where('is_active', true)->get();
        $maxPrice = ProductVariant::max('price');

        return view('frontend.products.index', [
            'products' => $products,
            'categories' => $filterCategories,
            'brands' => $filterBrands,
            'max_price' => $maxPrice ?? 50000000
        ]);
    }
    /**
     * Hiển thị trang chi tiết của một sản phẩm.
     */
    public function show(string $slug)
    {
        // Tải sản phẩm cùng các quan hệ cần thiết để tối ưu hiệu năng
        $product = Product::where('slug', $slug)
            ->active()
            ->with([
                'categories', 
                'brand', 
                'images', 
                'variants', 
                'approvedReviews.user.profile' // Tải các đánh giá đã được duyệt và thông tin người dùng
            ])
            ->firstOrFail();
            
        // Tạo nhóm thuộc tính để hiển thị các lựa chọn (Màu sắc, Kích thước...)
        $attributeGroups = [];
        foreach ($product->variants as $variant) {
            foreach ((array)$variant->attributes as $key => $value) {
                if (!isset($attributeGroups[$key])) {
                    $attributeGroups[$key] = [];
                }
                if (!in_array($value, $attributeGroups[$key])) {
                    $attributeGroups[$key][] = $value;
                }
            }
        }
        
        // Lấy sản phẩm liên quan (cùng danh mục, trừ sản phẩm hiện tại)
        $relatedProducts = Product::whereHas('categories', function ($query) use ($product) {
            // Chỉ định rõ 'categories.id' để tránh lỗi ambiguous column
            $query->whereIn('categories.id', $product->categories->pluck('id'));
        })
        // Chỉ định rõ 'products.id' để loại trừ sản phẩm đang xem
        ->where('products.id', '!=', $product->id)
        ->active()
        ->with(['variants', 'images']) // Tải sẵn thông tin cho thẻ sản phẩm
        ->latest()
        ->take(4) // Lấy 4 sản phẩm
        ->get();
            
        // Gửi tất cả dữ liệu cần thiết sang view
        return view('frontend.products.show', compact('product', 'attributeGroups', 'relatedProducts'));
    }
}
