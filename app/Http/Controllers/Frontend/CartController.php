<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Inventory;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    /**
     * Hiển thị trang giỏ hàng.
     */
    public function index()
    {
        $cartItems = Cart::where('user_id', Auth::id())
            ->with([
                'variant.product.images' => fn($q) => $q->where('is_primary', true)
            ])
            ->latest()
            ->get();

        $totalPrice = 0;
        foreach ($cartItems as $item) {
            $price = $item->variant->sale_price > 0 && $item->variant->sale_price < $item->variant->price
                ? $item->variant->sale_price
                : $item->variant->price;
            $totalPrice += $price * $item->quantity;
        }

        return view('frontend.cart.index', compact('cartItems', 'totalPrice'));
    }

    /**
     * Thêm một sản phẩm vào giỏ hàng.
     */
    public function add(Request $request)
    {
        // === SỬA ĐỔI QUAN TRỌNG Ở ĐÂY ===
        $request->validate([
            'product_variant_id' => 'required|exists:product_variants,id', // Sửa từ 'variant_id'
            'quantity' => 'required|integer|min:1',
        ]);

        $variantId = $request->product_variant_id; // Sửa từ 'variant_id'
        // === KẾT THÚC SỬA ĐỔI ===
        
        $quantityToAdd = $request->quantity;
        $userId = Auth::id();

        // Tìm biến thể và kho hàng tương ứng
        $variant = ProductVariant::with('product')->findOrFail($variantId);
        $inventory = Inventory::where('product_variant_id', $variantId)->first();

        $stock = $inventory ? $inventory->quantity : 0;

        // Lấy số lượng hiện có trong giỏ hàng
        $cartItem = Cart::where('user_id', $userId)
                          ->where('product_variant_id', $variantId)
                          ->first();
        
        $currentQuantityInCart = $cartItem ? $cartItem->quantity : 0;

        // KIỂM TRA TỒN KHO
        if (($currentQuantityInCart + $quantityToAdd) > $stock) {
            return back()->with('error', "Rất tiếc, sản phẩm '{$variant->product->name}' chỉ còn {$stock} sản phẩm trong kho.");
        }

        // Nếu đủ hàng, tiến hành thêm/cập nhật
        if ($cartItem) {
            $cartItem->quantity += $quantityToAdd;
            $cartItem->save();
        } else {
            Cart::create([
                'user_id' => $userId,
                'product_variant_id' => $variantId,
                'quantity' => $quantityToAdd,
            ]);
        }

        return redirect()->route('cart.index')->with('success', 'Đã thêm sản phẩm vào giỏ hàng!');
    }

    /**
     * Cập nhật số lượng sản phẩm trong giỏ hàng.
     */
    public function update(Request $request, $cartId)
    {
        $request->validate(['quantity' => 'required|integer|min:1']);

        $newQuantity = $request->quantity;
        $cartItem = Cart::where('id', $cartId)->where('user_id', Auth::id())->firstOrFail();
        
        $inventory = Inventory::where('product_variant_id', $cartItem->product_variant_id)->first();
        $stock = $inventory ? $inventory->quantity : 0;

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
}