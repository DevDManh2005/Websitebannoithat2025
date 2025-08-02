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
    public function index()
    {
        $inventories = Inventory::with(['product', 'variant', 'location'])->paginate(15);
        return view('admins.inventories.index', compact('inventories'));
    }

    public function create()
    {
        $products = Product::where('is_active', true)->orderBy('name')->get();
        // Lấy tất cả biến thể để JavaScript có thể lọc
        $variants = ProductVariant::with('product:id,name')->get(['id', 'product_id', 'sku', 'attributes']);
        return view('admins.inventories.create', compact('products', 'variants'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'product_variant_id' => 'nullable|exists:product_variants,id',
            'quantity' => 'required|integer|min:0',
            'address' => 'nullable|string|max:500', // Chỉ cần trường địa chỉ chung
        ]);

        try {
            DB::beginTransaction();

            $location = Location::create([
                'name' => 'Kho hàng cho sản phẩm #' . $data['product_id'],
                'address' => $data['address'],
            ]);

            Inventory::create([
                'product_id' => $data['product_id'],
                'product_variant_id' => $data['product_variant_id'],
                'quantity' => $data['quantity'],
                'location_id' => $location->id,
            ]);

            DB::commit();
            return redirect()->route('admin.inventories.index')->with('success', 'Tạo bản ghi kho thành công.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi khi tạo kho: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Đã xảy ra lỗi khi tạo kho.');
        }
    }

    public function show(Inventory $inventory)
    {
        $inventory->load(['product', 'variant', 'location', 'transactions.user']);
        return view('admins.inventories.show', compact('inventory'));
    }

    public function edit(Inventory $inventory)
    {
        $inventory->load('location');
        $products = Product::where('is_active', true)->orderBy('name')->get();
        $variants = ProductVariant::with('product:id,name')->get(['id', 'product_id', 'sku', 'attributes']);
        return view('admins.inventories.edit', compact('inventory', 'products', 'variants'));
    }

    public function update(Request $request, Inventory $inventory)
    {
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'product_variant_id' => 'nullable|exists:product_variants,id',
            'quantity' => 'required|integer|min:0',
            'address' => 'nullable|string|max:500', // Chỉ cần trường địa chỉ chung
        ]);
        
        try {
            DB::beginTransaction();

            if ($inventory->location) {
                $inventory->location->update(['address' => $data['address']]);
            } else {
                $location = Location::create([
                    'name' => 'Kho hàng cho sản phẩm #' . $data['product_id'],
                    'address' => $data['address'],
                ]);
                $inventory->location_id = $location->id;
            }
            
            $inventory->update($data);
            
            DB::commit();
            return redirect()->route('admin.inventories.index')->with('success', 'Cập nhật kho thành công.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi khi cập nhật kho: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Đã xảy ra lỗi khi cập nhật kho.');
        }
    }

    public function destroy(Inventory $inventory)
    {
        try {
            DB::beginTransaction();
            // Xóa location đi kèm nếu có
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