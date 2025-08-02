<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    public function index()
    {
        // Lấy danh sách ID sản phẩm từ wishlist của người dùng
        $wishlistProductIds = Auth::user()->wishlist()->pluck('product_id');

        // Lấy thông tin chi tiết của các sản phẩm đó
        $wishlistProducts = Product::whereIn('id', $wishlistProductIds)
            ->with(['variants', 'images']) // Tải sẵn các thông tin cần thiết
            ->paginate(12); // Phân trang để không tải quá nhiều cùng lúc

        return view('frontend.wishlist.index', compact('wishlistProducts'));
    }

    public function toggle(Request $request)
    {
        $request->validate(['product_id' => 'required|exists:products,id']);
        
        if (!Auth::check()) {
            return response()->json([
                'success' => false, 
                'message' => 'Vui lòng đăng nhập để sử dụng chức năng này.',
                'redirect' => route('login.form')
            ]);
        }

        $user = Auth::user();

        // Thêm hoặc xóa sản phẩm khỏi danh sách yêu thích
        $user->wishlist()->toggle($request->product_id);

        // Tải lại (refresh) mối quan hệ wishlist để lấy dữ liệu mới nhất
        $user->load('wishlist');

        // Kiểm tra lại trạng thái sau khi đã tải lại
        $isAdded = $user->wishlist->contains($request->product_id);

        return response()->json([
            'success' => true,
            'status' => $isAdded ? 'added' : 'removed',
            'count' => $user->wishlist()->count()
        ]);
    }
}
