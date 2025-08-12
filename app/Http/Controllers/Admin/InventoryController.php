<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InventoryController extends Controller
{
    /**
     * Hiển thị danh sách tồn kho.
     */
    public function index()
    {
        $inventories = Inventory::with(['product', 'variant', 'location'])->paginate(15);
        return view('admins.inventories.index', compact('inventories'));
    }

    /**
     * Hiển thị form để chọn sản phẩm và cập nhật kho hàng loạt.
     */
    public function create()
    {
        $products = Product::where('is_active', true)->has('variants')->orderBy('name')->get();
        return view('admins.inventories.create', compact('products'));
    }

    /**
     * Lưu hoặc cập nhật bản ghi kho hàng cho nhiều biến thể của một sản phẩm.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'variants' => 'required|array',
            'variants.*.id' => 'required|exists:product_variants,id',
            'variants.*.quantity' => 'required|integer|min:0',
            'variants.*.address' => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            foreach ($data['variants'] as $variantData) {
                $inventory = Inventory::firstOrNew([
                    'product_id' => $data['product_id'],
                    'product_variant_id' => $variantData['id'],
                ]);

                $inventory->quantity = $variantData['quantity'];
                
                // Cập nhật hoặc tạo mới địa chỉ kho
                if ($inventory->location) {
                    $inventory->location->update(['address' => $variantData['address']]);
                } else {
                    $location = Location::create([
                        'name' => 'Kho hàng cho biến thể #' . $variantData['id'],
                        'address' => $variantData['address'],
                    ]);
                    $inventory->location_id = $location->id;
                }
                
                $inventory->save();
            }

            DB::commit();
            return redirect()->route('admin.inventories.index')->with('success', 'Cập nhật kho hàng thành công.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi khi cập nhật kho: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Đã xảy ra lỗi khi cập nhật kho.');
        }
    }

    // Các phương thức show, edit, update, destroy cũ có thể được giữ lại hoặc xóa đi
    // vì trang create mới đã đảm nhiệm chức năng chính.
    
    public function show(Inventory $inventory)
    {
        $inventory->load(['product', 'variant', 'location', 'transactions.user']);
        return view('admins.inventories.show', compact('inventory'));
    }

    public function edit(Inventory $inventory)
    {
        // Chuyển hướng đến trang create mới vì logic đã được gộp chung
        return redirect()->route('admin.inventories.create');
    }

    public function update(Request $request, Inventory $inventory)
    {
        // Logic này đã được xử lý trong store()
        return redirect()->route('admin.inventories.index');
    }

    public function destroy(Inventory $inventory)
    {
        try {
            DB::beginTransaction();
            if ($inventory->location) {
                $inventory->location->delete();
            }
            $inventory->delete();
            DB::commit();
            return redirect()->route('admin.inventories.index')->with('success', 'Xóa bản ghi kho thành công.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi khi xóa kho: ' . $e->getMessage());
            return back()->with('error', 'Đã xảy ra lỗi khi xóa kho.');
        }
    }
}
