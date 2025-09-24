<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductReview;
use Illuminate\Http\Request;

class ProductReviewController extends Controller
{
    public function index(Request $request)
    {
        $reviews = ProductReview::with(['user', 'product'])->latest()->paginate(15);
        return view('admins.reviews.index', compact('reviews'));
    }

    /** Xem lịch sử chỉnh sửa/xoá từ file JSON */
    public function history(ProductReview $review)
    {
        $path = storage_path('app/reviews_history/'.$review->id.'.json');
        $data = [];
        if (file_exists($path)) {
            $json = @file_get_contents($path);
            $data = json_decode($json ?: '[]', true) ?: [];
        }
        return response()->json($data);
    }

    /** Xoá review (giữ nguyên như cũ) */
    public function destroy(ProductReview $review)
    {
        if ($review->image) {
            \Storage::disk('public')->delete($review->image);
        }
        $review->delete();
        return back()->with('success', 'Đã xóa đánh giá thành công.');
    }
}
