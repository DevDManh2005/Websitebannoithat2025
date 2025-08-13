<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\BlogComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BlogCommentController extends Controller
{
    public function store(Request $request, Blog $blog)
    {
        $data = $request->validate([
            'comment'   => 'required|string',
            'parent_id' => 'nullable|exists:blog_comments,id',
        ]);

        $comment = new BlogComment();
        $comment->blog_id    = $blog->id;
        $comment->user_id    = Auth::id();
        $comment->comment    = $data['comment'];
        $comment->parent_id  = $data['parent_id'] ?? null;
        $comment->is_approved = 1; // hoặc 0 nếu muốn duyệt tay
        $comment->save();

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Đã gửi bình luận.',
                'data'    => $comment->load('user'),
            ]);
        }

        return back()->with('success', 'Đã gửi bình luận.');
    }
}
