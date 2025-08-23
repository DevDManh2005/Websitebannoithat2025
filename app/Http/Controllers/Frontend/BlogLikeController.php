<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\BlogLike;

class BlogLikeController extends Controller
{
    public function toggle(Blog $blog)
    {
        $like = BlogLike::where('blog_id', $blog->id)->where('user_id', auth()->id())->first();

        if ($like) {
            $like->delete();
            $msg = 'Đã bỏ tym.';
            $liked = false;
        } else {
            BlogLike::create(['blog_id' => $blog->id, 'user_id' => auth()->id()]);
            $msg = 'Đã thả tym.';
            $liked = true;
        }

        $count = $blog->likes()->count();

        if (request()->wantsJson()) {
            return response()->json([
                'message' => $msg,
                'liked' => $liked,
                'count' => $count,
            ]);
        }

        return back()->with('success', $msg);
    }
}
