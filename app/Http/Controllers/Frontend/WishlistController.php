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
        $wishlistProductIds = Auth::user()->wishlist()->pluck('product_id');
        $wishlistProducts = Product::whereIn('id', $wishlistProductIds)
            ->with(['variants', 'images'])
            ->paginate(12);

        return view('frontend.wishlist.index', compact('wishlistProducts'));
    }

    public function toggle(Request $request)
    {
        $request->validate(['product_id' => 'required|exists:products,id']);

        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'redirect' => route('login.form')
            ]);
        }

        $user = Auth::user();
        $result = $user->wishlist()->toggle($request->product_id);
        $isAdded = !empty($result['attached']);

        return response()->json([
            'success' => true,
            'status' => $isAdded ? 'added' : 'removed'
        ]);
    }
}
