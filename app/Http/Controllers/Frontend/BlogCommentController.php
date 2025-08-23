<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\BlogComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class BlogCommentController extends Controller
{
    public function store(Request $request, Blog $blog)
    {
        // 1. Kiểm tra dữ liệu
        $validator = Validator::make($request->all(), [
            'comment'   => 'required|string|max:1000',
            'parent_id' => 'nullable|exists:blog_comments,id',
        ]);

        if ($validator->fails()) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        // 2. Lưu bình luận bằng relationship method cho code gọn hơn
        $comment = $blog->comments()->create([
            'user_id'   => Auth::id(),
            'comment'   => $request->comment,
            'parent_id' => $request->parent_id,
            'is_approved' => 1, // Tự động duyệt
        ]);

        // 3. Tạo URL để chuyển hướng tới, có chứa anchor đến comment mới
        $redirectUrl = route('blog.show', $blog->slug) . '#comment-' . $comment->id;

        // 4. Nếu là yêu cầu AJAX, trả về JSON chứa URL
        if ($request->wantsJson()) {
            return response()->json([
                'success'     => true,
                'redirectUrl' => $redirectUrl,
            ]);
        }

        // 5. Nếu là submit thông thường (không có JS), redirect như cũ nhưng có anchor
        return redirect($redirectUrl)->with('success', 'Bình luận của bạn đã được gửi!');
    }
}