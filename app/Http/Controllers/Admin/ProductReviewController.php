<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductReviewController extends Controller
{
    public function index(Request $request)
    {
        $reviews = ProductReview::with(['user', 'product'])->latest()->paginate(15);
        return view('admins.reviews.index', compact('reviews'));
    }

    public function toggleStatus(ProductReview $review)
    {
        // Lật ngược trạng thái: nếu đang chờ thì duyệt, nếu đã duyệt thì chuyển về chờ
        $newStatus = $review->status === 'approved' ? 'pending' : 'approved';
        $review->update(['status' => $newStatus]);

        $message = $newStatus === 'approved' ? 'Đã duyệt đánh giá.' : 'Đã ẩn đánh giá.';
        return back()->with('success', $message);
    }
    
    public function destroy(ProductReview $review)
    {
        if ($review->image) {
            Storage::disk('public')->delete($review->image);
        }
        $review->delete();

        return back()->with('success', 'Đã xóa đánh giá thành công.');
    }
}