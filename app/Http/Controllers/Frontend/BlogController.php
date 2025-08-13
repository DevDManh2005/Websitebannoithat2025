<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\BlogCategory;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $categorySlug = $request->query('danh-muc');
        $query = Blog::with(['category','author'])->published()->latest();

        if ($categorySlug) {
            $cat = BlogCategory::where('slug', $categorySlug)->first();
            if ($cat) $query->where('category_id', $cat->id);
        }
        if ($s = $request->query('q')) {
            $query->where(fn($q)=>$q->where('title','like',"%$s%")->orWhere('excerpt','like',"%$s%"));
        }

        $posts = $query->paginate(9)->appends($request->query());
        $cats  = BlogCategory::where('is_active',1)->orderBy('sort_order')->get();

        return view('frontend.blog.index', compact('posts','cats','categorySlug'));
    }

    public function show($slug)
    {
        $post = Blog::where('slug',$slug)->with([
            'category','author',
            'comments.children.user',
            'comments.user'
        ])->firstOrFail();

        // tăng view
        $post->increment('view_count');

        return view('frontend.blog.show', compact('post'));
    }
}
