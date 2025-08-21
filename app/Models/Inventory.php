<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'product_variant_id',
        'quantity',     // cột tồn kho chính (nếu DB bạn đặt tên khác vẫn OK nhờ accessor bên dưới)
        'location_id',
    ];

    /** Quan hệ */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function transactions()
    {
        return $this->hasMany(InventoryTransaction::class);
    }

    /**
     * Thuộc tính ảo: số tồn để hiển thị an toàn (tự dò cột nếu quantity không có)
     * Dùng trong view: $inventory->stock_display
     */
    public function getStockDisplayAttribute()
    {
        return $this->quantity
            ?? $this->stock
            ?? $this->qty
            ?? $this->stock_quantity
            ?? $this->available
            ?? $this->available_quantity
            ?? 0;
    }
}
