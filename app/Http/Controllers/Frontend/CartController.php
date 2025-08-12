<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    /**
     * Hiển thị trang giỏ hàng.
     */
    public function index()
    {
        $cartItems = Cart::where('user_id', Auth::id())
            ->with(['variant.product.primaryImage'])
            ->latest()
            ->get();

        // Tính tổng tiền của các sản phẩm đã được chọn
        $totalPrice = $cartItems->where('is_selected', true)->sum(function ($item) {
            $price = $item->variant->sale_price ?: $item->variant->price;
            return $price * $item->quantity;
        });

        return view('frontend.cart.index', compact('cartItems', 'totalPrice'));
    }

    /**
     * Thêm một sản phẩm vào giỏ hàng (Xử lý AJAX).
     * Logic: Thêm vào giỏ hàng hiện tại hoặc tăng số lượng nếu đã có.
     */
    public function add(Request $request)
    {
        $validated = $request->validate([
            'product_variant_id' => 'required|exists:product_variants,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $variantId = $validated['product_variant_id'];
        $quantityToAdd = $validated['quantity'];
        $userId = Auth::id();

        $variant = ProductVariant::with('product', 'inventory')->findOrFail($variantId);
        $stock = optional($variant->inventory)->quantity ?? 0;

        $cartItem = Cart::where('user_id', $userId)->where('product_variant_id', $variantId)->first();
        $currentQuantityInCart = $cartItem ? $cartItem->quantity : 0;

        if (($currentQuantityInCart + $quantityToAdd) > $stock) {
            return response()->json([
                'success' => false, 
                'message' => "Rất tiếc, sản phẩm '{$variant->product->name}' chỉ còn {$stock} sản phẩm."
            ], 422);
        }

        if ($cartItem) {
            $cartItem->increment('quantity', $quantityToAdd);
        } else {
            Cart::create([
                'user_id' => $userId,
                'product_variant_id' => $variantId,
                'quantity' => $quantityToAdd,
                'is_selected' => true // Mặc định chọn khi thêm mới
            ]);
        }

        $cartCount = Cart::where('user_id', $userId)->count();

        return response()->json([
            'success' => true,
            'message' => 'Đã thêm sản phẩm vào giỏ hàng!',
            'cart_count' => $cartCount
        ]);
    }

    /**
     * Xóa giỏ hàng cũ, thêm sản phẩm mới và chuyển đến trang thanh toán.
     */
    public function buyNow(Request $request)
    {
        $validated = $request->validate([
            'product_variant_id' => 'required|exists:product_variants,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $variantId = $validated['product_variant_id'];
        $quantity = $validated['quantity'];
        $userId = Auth::id();

        $variant = ProductVariant::with('product', 'inventory')->findOrFail($variantId);
        $stock = optional($variant->inventory)->quantity ?? 0;

        if ($quantity > $stock) {
            return response()->json(['success' => false, 'message' => "Rất tiếc, sản phẩm '{$variant->product->name}' chỉ còn {$stock} sản phẩm."], 422);
        }
        
        DB::transaction(function () use ($userId, $variantId, $quantity) {
            // Bước 1: Xóa toàn bộ giỏ hàng hiện tại của người dùng
            Cart::where('user_id', $userId)->delete();

            // Bước 2: Thêm duy nhất sản phẩm "Mua ngay" vào giỏ hàng và đánh dấu đã chọn
            Cart::create([
                'user_id' => $userId, 
                'product_variant_id' => $variantId, 
                'quantity' => $quantity, 
                'is_selected' => true
            ]);
        });

        // Bước 3: Chuyển hướng đến trang thanh toán
        return response()->json(['success' => true, 'redirect_url' => route('checkout.index')]);
    }

    /**
     * Cập nhật số lượng sản phẩm trong giỏ hàng.
     */
    public function update(Request $request, $cartId)
    {
        $request->validate(['quantity' => 'required|integer|min:1']);
        $newQuantity = $request->quantity;
        $cartItem = Cart::where('id', $cartId)->where('user_id', Auth::id())->firstOrFail();
        $stock = optional($cartItem->variant->inventory)->quantity ?? 0;

        if ($newQuantity > $stock) {
            return back()->with('error', "Rất tiếc, sản phẩm này chỉ còn {$stock} sản phẩm trong kho.");
        }

        $cartItem->quantity = $newQuantity;
        $cartItem->save();
        return back()->with('success', 'Cập nhật số lượng thành công!');
    }

    /**
     * Xóa một sản phẩm khỏi giỏ hàng.
     */
    public function remove($cartId)
    {
        Cart::where('id', $cartId)->where('user_id', Auth::id())->firstOrFail()->delete();
        return back()->with('success', 'Đã xóa sản phẩm khỏi giỏ hàng!');
    }

    /**
     * Cập nhật trạng thái chọn của các sản phẩm trong giỏ hàng.
     */
    public function toggleSelect(Request $request)
    {
        $request->validate(['items' => 'present|array']);
        $userId = Auth::id();

        DB::transaction(function () use ($request, $userId) {
            Cart::where('user_id', $userId)->update(['is_selected' => false]);
            if (!empty($request->items)) {
                Cart::where('user_id', $userId)->whereIn('id', $request->items)->update(['is_selected' => true]);
            }
        });

        $selectedItems = Cart::where('user_id', $userId)->where('is_selected', true)->with('variant')->get();
        $totalPrice = $selectedItems->sum(function ($item) {
            $price = $item->variant->sale_price ?: $item->variant->price;
            return $price * $item->quantity;
        });

        return response()->json([
            'success' => true,
            'total_price_html' => number_format($totalPrice) . ' ₫'
        ]);
    }

    /**
     * Xóa các sản phẩm đã được chọn.
     */
    public function removeSelected()
    {
        Cart::where('user_id', Auth::id())->where('is_selected', true)->delete();
        return back()->with('success', 'Đã xóa các sản phẩm được chọn khỏi giỏ hàng.');
    }
}