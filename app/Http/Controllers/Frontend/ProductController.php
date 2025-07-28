<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Hiển thị trang chi tiết của một sản phẩm.
     *
     * @param  string  $slug
     * @return \Illuminate\View\View
     */
    public function show(string $slug)
    {
        // Tìm sản phẩm active theo slug, nếu không thấy sẽ báo lỗi 404
        // Eager load tất cả các quan hệ cần thiết để tối ưu và hiển thị
        $product = Product::where('slug', $slug)
            ->active()
            ->with(['category', 'brand', 'images', 'variants'])
            ->firstOrFail();
            
        return view('frontend.products.show', compact('product'));
    }
}