<?php

namespace App\Services;

use App\Models\{Inventory, InventoryTransaction, Order, ProductVariant};
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class InventoryService
{
    public function deductForOrder(Order $order, ?int $actorId = null): void
    {
        DB::transaction(function () use ($order, $actorId) {
            $items = $order->items()->lockForUpdate()->get();

            foreach ($items as $item) {
                $variant = ProductVariant::findOrFail($item->product_variant_id);
                $inv = Inventory::where('product_id', $variant->product_id)
                    ->where('product_variant_id', $variant->id)
                    ->lockForUpdate()
                    ->first();

                if (!$inv) {
                    throw ValidationException::withMessages([
                        'inventory' => "Chưa khai báo tồn kho cho SKU {$variant->sku}.",
                    ]);
                }
                if ($inv->quantity < $item->quantity) {
                    throw ValidationException::withMessages([
                        'inventory' => "Không đủ tồn kho (SKU {$variant->sku}).",
                    ]);
                }

                // chống trừ lặp cho từng item
                $descKey = 'order '.$order->order_code.' item#'.$item->id;
                $already = InventoryTransaction::where('inventory_id', $inv->id)
                    ->where('type', InventoryTransaction::TYPE_OUT)
                    ->where('description', 'like', '%'.$descKey.'%')
                    ->exists();
                if ($already) continue;

                $inv->decrement('quantity', $item->quantity);

                InventoryTransaction::create([
                    'inventory_id' => $inv->id,
                    'type'         => InventoryTransaction::TYPE_OUT,
                    'quantity'     => $item->quantity,
                    'description'  => 'Trừ kho theo đơn '.$order->order_code.' ('.$descKey.')',
                    'user_id'      => $actorId,
                ]);
            }
        });
    }

    public function restockForOrder(Order $order, ?int $actorId = null): void
    {
        DB::transaction(function () use ($order, $actorId) {
            $items = $order->items()->lockForUpdate()->get();

            foreach ($items as $item) {
                $variant = ProductVariant::findOrFail($item->product_variant_id);
                $inv = Inventory::firstOrCreate(
                    ['product_id' => $variant->product_id, 'product_variant_id' => $variant->id],
                    ['quantity' => 0]
                );

                $descKey = 'RESTOCK order '.$order->order_code.' item#'.$item->id;
                $already = InventoryTransaction::where('inventory_id', $inv->id)
                    ->where('type', InventoryTransaction::TYPE_IN)
                    ->where('description', 'like', '%'.$descKey.'%')
                    ->exists();
                if ($already) continue;

                $inv->increment('quantity', $item->quantity);

                InventoryTransaction::create([
                    'inventory_id' => $inv->id,
                    'type'         => InventoryTransaction::TYPE_IN,
                    'quantity'     => $item->quantity,
                    'description'  => 'Hoàn kho đơn '.$order->order_code.' ('.$descKey.')',
                    'user_id'      => $actorId,
                ]);
            }
        });
    }
}
