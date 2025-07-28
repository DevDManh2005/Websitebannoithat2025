<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    /**
     * Hiển thị trang danh sách yêu thích của người dùng.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Lấy danh sách sản phẩm mà user hiện tại đã yêu thích
        $wishlistProducts = Auth::user()
            ->wishlist() // Sử dụng relationship đã định nghĩa ở User model
            ->with([
                'images' => fn($q) => $q->where('is_primary', true),
                'variants' => fn($q) => $q->where('is_main_variant', true)
            ])
            ->latest()
            ->get();

        return view('frontend.wishlist.index', compact('wishlistProducts'));
    }

    /**
     * Thêm hoặc xóa một sản phẩm khỏi danh sách yêu thích.
     * Hàm này được gọi bằng JavaScript (AJAX).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggle(Request $request)
    {
        // Bắt buộc đăng nhập
        if (!Auth::check()) {
            return response()->json(['status' => 'error', 'message' => 'Vui lòng đăng nhập để sử dụng chức năng này.'], 401);
        }

        $request->validate(['product_id' => 'required|exists:products,id']);

        $user = Auth::user();
        $productId = $request->product_id;

        // toggle() là một phương thức tiện lợi của quan hệ nhiều-nhiều.
        // Nó sẽ tự động thêm nếu chưa có, và xóa nếu đã có.
        $result = $user->wishlist()->toggle($productId);

        // 'attached' nghĩa là sản phẩm vừa được thêm vào
        $isAdded = count($result['attached']) > 0;

        return response()->json([
            'status' => 'success',
            'is_added' => $isAdded,
            'message' => $isAdded ? 'Đã thêm vào danh sách yêu thích!' : 'Đã xóa khỏi danh sách yêu thích.'
        ]);
    }
}
