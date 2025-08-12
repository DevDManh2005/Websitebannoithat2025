<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('q');
        
        // Chuyển từ khóa tìm kiếm về chữ thường
        $lowerQuery = strtolower($query);

        // Tải sẵn các quan hệ để tối ưu và phân trang kết quả
        // SỬA ĐỔI: Sử dụng whereRaw để tìm kiếm không phân biệt hoa-thường
        $products = Product::whereRaw('LOWER(name) LIKE ?', ["%{$lowerQuery}%"])
            ->active()
            ->with(['variants', 'images'])
            ->paginate(16);

        // Trỏ đến view mới: frontend.search.index
        return view('frontend.search.index', compact('products', 'query'));
    }
}