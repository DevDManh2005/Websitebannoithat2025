<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Location;
use App\Models\InventoryTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InventoryController extends Controller
{
    /**
     * Danh sách tồn kho + bộ lọc.
     * q: tìm theo tên SP / SKU, low: zero|low5
     */
    public function index(Request $request)
    {
        $q   = $request->input('q');
        $low = $request->input('low'); // zero | low5

        $query = Inventory::with(['product', 'variant', 'location']);

        if ($q) {
            $query->where(function ($sub) use ($q) {
                $sub->whereHas('product', function ($q1) use ($q) {
                    $q1->where('name', 'like', "%{$q}%");
                })
                    ->orWhereHas('variant', function ($q2) use ($q) {
                        $q2->where('sku', 'like', "%{$q}%");
                    });
            });
        }


        if ($low === 'zero') {
            $query->where('quantity', 0);
        } elseif ($low === 'low5') {
            $query->where('quantity', '<=', 5);
        }

        $inventories = $query->latest()->paginate(15);

        return view('admins.inventories.index', compact('inventories'));
    }

    /**
     * Form cập nhật hàng loạt theo sản phẩm.
     */
    public function create()
    {
        $products = Product::where('is_active', true)->has('variants')->orderBy('name')->get();
        return view('admins.inventories.create', compact('products'));
    }

    /**
     * API JSON: Danh sách biến thể của 1 sản phẩm + tồn kho hiện tại (nếu có).
     * Route gợi ý (GET): admin/products/{productId}/variants-inventory  ->  inventories.variants
     */
    public function variantsInventory($productId)
    {
        $variants = ProductVariant::where('product_id', $productId)
            ->select(['id', 'product_id', 'sku', 'attributes'])
            ->get();

        $invByVariant = Inventory::with('location')
            ->where('product_id', $productId)
            ->whereIn('product_variant_id', $variants->pluck('id'))
            ->get()
            ->keyBy('product_variant_id');

        $data = $variants->map(function ($v) use ($invByVariant) {
            $inv = $invByVariant->get($v->id);
            return [
                'id'         => $v->id,
                'sku'        => $v->sku,
                'attributes' => is_array($v->attributes) ? $v->attributes : (json_decode($v->attributes ?? '[]', true) ?: []),
                'inventory'  => $inv ? [
                    'quantity' => (int) $inv->quantity,
                    'location' => [
                        'id'      => $inv->location_id,
                        'address' => optional($inv->location)->address,
                        'name'    => optional($inv->location)->name,
                    ],
                ] : null,
            ];
        });

        return response()->json($data);
    }

    /**
     * Lưu/cập nhật nhiều biến thể cho 1 sản phẩm.
     */
    public function store(Request $request)
{
    $data = $request->validate([
        'product_id'            => 'required|exists:products,id',
        'variants'              => 'required|array',
        'variants.*.id'         => 'required|exists:product_variants,id',
        'variants.*.quantity'   => 'required|integer|min:0',
        'variants.*.address'    => 'nullable|string|max:500',
        // nếu muốn cho phép ghi chú giao dịch:
        'variants.*.note'       => 'nullable|string|max:1000',
    ]);

    try {
        DB::beginTransaction();

        $userId = auth()->id();

        foreach ($data['variants'] as $v) {
            // Tìm/khởi tạo bản ghi tồn kho cho biến thể
            $inventory = Inventory::firstOrNew([
                'product_id'         => $data['product_id'],
                'product_variant_id' => $v['id'],
            ]);

            $oldQty = (int) ($inventory->quantity ?? 0);
            $newQty = (int) $v['quantity'];

            // Cập nhật hoặc tạo mới location
            $addr = $v['address'] ?? null;
            if ($inventory->exists && $inventory->location) {
                // Giữ nguyên hành vi cũ: update luôn (kể cả null để xóa địa chỉ)
                $inventory->location->update(['address' => $addr]);
            } else {
                $location = Location::create([
                    'name'    => 'Kho biến thể #' . $v['id'],
                    'address' => $addr,
                ]);
                $inventory->location_id = $location->id;
            }

            // Lưu số lượng mới
            $inventory->quantity = $newQty;
            $inventory->save();

            // Ghi lịch sử giao dịch nếu có thay đổi
            $delta = $newQty - $oldQty;
            if ($delta !== 0) {
                InventoryTransaction::create([
                    'inventory_id' => $inventory->id,
                    'type'         => $delta > 0 ? InventoryTransaction::TYPE_IN : InventoryTransaction::TYPE_OUT, // 'in' | 'out'
                    'quantity'     => abs($delta),
                    'description'  => $v['note'] ?? ('Cập nhật số lượng từ ' . $oldQty . ' → ' . $newQty),
                    'user_id'      => $userId,
                ]);
            }
            // Nếu delta = 0 thì bỏ qua, vì DB chưa hỗ trợ 'adjust'
        }

        DB::commit();
        return redirect()
            ->route('admin.inventories.index')
            ->with('success', 'Cập nhật kho hàng thành công.');
    } catch (\Throwable $e) {
        DB::rollBack();
        Log::error('Inventory bulk update failed: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
        return back()->withInput()->with('error', 'Đã xảy ra lỗi khi cập nhật kho.');
    }
}


    public function show(Inventory $inventory)
    {
        $inventory->load(['product', 'variant', 'location', 'transactions.user']);
        return view('admins.inventories.show', compact('inventory'));
    }

    /**
     * Chỉnh sửa từng bản ghi kho riêng lẻ.
     */
    public function edit(Inventory $inventory)
    {
        $products = Product::where('is_active', true)->orderBy('name')->get();
        // Tải sẵn toàn bộ variants (client sẽ lọc theo product_id)
        $variants = ProductVariant::select(['id', 'product_id', 'sku', 'attributes'])->get();

        return view('admins.inventories.edit', compact('inventory', 'products', 'variants'));
    }

    public function update(Request $request, Inventory $inventory)
{
    $data = $request->validate([
        'product_id'         => 'required|exists:products,id',
        'product_variant_id' => 'nullable|exists:product_variants,id',
        'quantity'           => 'required|integer|min:0',
        'address'            => 'nullable|string|max:500',
        // tùy chọn: ghi chú giao dịch
        'note'               => 'nullable|string|max:1000',
    ]);

    try {
        DB::beginTransaction();

        // Lưu lại trạng thái cũ
        $oldQty       = (int) ($inventory->quantity ?? 0);
        $oldVariantId = $inventory->product_variant_id;

        // Cập nhật thông tin chính
        $inventory->product_id         = $data['product_id'];
        $inventory->product_variant_id = $data['product_variant_id'] ?: null;

        $newQty = (int) $data['quantity'];
        $inventory->quantity = $newQty;

        // Cập nhật / tạo Location
        $addr = $data['address'] ?? null;
        if ($inventory->location) {
            $inventory->location->update(['address' => $addr]);
        } else {
            $location = Location::create([
                'name'    => 'Kho sản phẩm #' . $inventory->product_id,
                'address' => $addr,
            ]);
            $inventory->location_id = $location->id;
        }

        $inventory->save();

        // Ghi lịch sử nếu có thay đổi số lượng
        $delta = $newQty - $oldQty;
        if ($delta !== 0) {
            $variantChanged = $oldVariantId != $inventory->product_variant_id;

            InventoryTransaction::create([
                'inventory_id' => $inventory->id,
                'type'         => $delta > 0
                                  ? InventoryTransaction::TYPE_IN   // 'in'
                                  : InventoryTransaction::TYPE_OUT, // 'out'
                'quantity'     => abs($delta),
                'description'  => $data['note']
                                  ?? ('Cập nhật số lượng từ ' . $oldQty . ' → ' . $newQty
                                      . ($variantChanged ? ('; biến thể: ' . ($oldVariantId ?: 'null')
                                      . ' → ' . ($inventory->product_variant_id ?: 'null')) : '')),
                'user_id'      => auth()->id(),
            ]);
        }

        DB::commit();
        return redirect()->route('admin.inventories.index')->with('success', 'Đã cập nhật kho.');
    } catch (\Throwable $e) {
        DB::rollBack();
        Log::error('Inventory update failed: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
        return back()->withInput()->with('error', 'Cập nhật thất bại.');
    }
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
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Inventory delete failed: ' . $e->getMessage());
            return back()->with('error', 'Đã xảy ra lỗi khi xóa kho.');
        }
    }
}
