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
        
        // Tải sẵn các quan hệ để tối ưu và phân trang kết quả
        $products = Product::where('name', 'like', "%{$query}%")
            ->active()
            ->with(['variants', 'images'])
            ->paginate(16);

        // Trỏ đến view mới: frontend.search.index
        return view('frontend.search.index', compact('products', 'query'));
    }
}
