<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Slide; // <-- Bổ sung dòng này
use Illuminate\Http\Request;

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
            
        // Gửi tất cả dữ liệu sang view
        return view('index', compact('slides', 'featuredProducts', 'latestProducts'));
    }
}