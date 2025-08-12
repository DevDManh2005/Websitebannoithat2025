<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductReviewController extends Controller
{
   public function store(Request $request, Product $product)
{
    $user = Auth::user();

    // ✅ Cho phép 1 người chỉ đánh giá 1 lần
    if ($user->hasReviewedProduct($product->id)) {
        return back()->with('error', 'Bạn đã đánh giá sản phẩm này rồi.');
    }

    // ✅ Xét quyền mua hoặc là admin/nhân viên
    if (!$user->hasPurchasedProduct($product->id) && !in_array($user->role->name ?? '', ['admin', 'nhanvien'])) {
        return back()->with('error', 'Bạn không thể đánh giá sản phẩm này.');
    }

    $request->validate([
        'rating' => 'required|integer|min:1|max:5',
        'review' => 'required|string|max:1000',
        'image' => 'nullable|image|max:2048',
    ]);

    $imagePath = null;
    if ($request->hasFile('image')) {
        $imagePath = $request->file('image')->store('reviews', 'public');
    }

    ProductReview::create([
        'user_id' => $user->id,
        'product_id' => $product->id,
        'rating' => $request->rating,
        'review' => $request->review,
        'image' => $imagePath,
        'status' => 'pending',
    ]);

    return back()->with('success', 'Cảm ơn bạn đã gửi đánh giá! Đánh giá của bạn sẽ được hiển thị sau khi được duyệt.');
}

}
