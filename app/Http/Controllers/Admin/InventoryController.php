<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Location;
use Illuminate\Http\Request;
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
        $variants = ProductVariant::with('product')->get();
        // Không cần truyền dữ liệu địa chỉ từ đây nữa, JS sẽ tự gọi API
        return view('admins.inventories.create', compact('products', 'variants'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'product_variant_id' => 'nullable|exists:product_variants,id',
            'quantity' => 'required|integer|min:0',
            'address' => 'nullable|string|max:255',
            // Tên địa chỉ sẽ được gửi lên từ form (do JS điền vào)
            'city_name' => 'required|string|max:255',
            'district_name' => 'required|string|max:255',
            'ward_name' => 'required|string|max:255',
        ]);

        try {
            // Tạo hoặc tìm Location dựa trên thông tin
            $location = Location::create([
                'name' => $data['address'] ?? 'Kho hàng',
                'address' => $data['address'],
                'city_name' => $data['city_name'],
                'district_name' => $data['district_name'],
                'ward_name' => $data['ward_name'],
            ]);

            $inventoryData = $request->only(['product_id', 'product_variant_id', 'quantity']);
            $inventoryData['location_id'] = $location->id;
            
            Inventory::create($inventoryData);

            return redirect()->route('admin.inventories.index')->with('success', 'Tạo bản ghi kho thành công');
        } catch (\Exception $e) {
            Log::error('Lỗi khi tạo kho: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Lỗi khi tạo kho: ' . $e->getMessage());
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
        $variants = ProductVariant::with('product')->get();
        // Không cần truyền dữ liệu địa chỉ từ đây nữa
        return view('admins.inventories.edit', compact('inventory', 'products', 'variants'));
    }

    public function update(Request $request, Inventory $inventory)
    {
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'product_variant_id' => 'nullable|exists:product_variants,id',
            'quantity' => 'required|integer|min:0',
            'address' => 'nullable|string|max:255',
            'city_name' => 'required|string|max:255',
            'district_name' => 'required|string|max:255',
            'ward_name' => 'required|string|max:255',
        ]);

        try {
            $locationDetails = [
                'name' => $data['address'] ?? 'Kho hàng',
                'address' => $data['address'],
                'city_name' => $data['city_name'],
                'district_name' => $data['district_name'],
                'ward_name' => $data['ward_name'],
            ];
            
            if ($inventory->location) {
                $inventory->location->update($locationDetails);
            } else {
                $location = Location::create($locationDetails);
                $inventory->location_id = $location->id;
            }
            
            $inventory->update($request->only(['product_id', 'product_variant_id', 'quantity']));

            return redirect()->route('admin.inventories.index')->with('success', 'Cập nhật bản ghi kho thành công');
        } catch (\Exception $e) {
            Log::error('Lỗi khi cập nhật kho: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Lỗi khi cập nhật kho: ' . $e->getMessage());
        }
    }

    public function destroy(Inventory $inventory)
    {
        try {
            if($inventory->location){
                $inventory->location->delete();
            }
            $inventory->delete();
            return redirect()->route('admin.inventories.index')->with('success', 'Xóa bản ghi kho thành công');
        } catch (\Exception $e) {
            Log::error('Lỗi khi xóa kho: ' . $e->getMessage());
            return back()->with('error', 'Lỗi khi xóa kho: ' . $e->getMessage());
        }
    }
}
