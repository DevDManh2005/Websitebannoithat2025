<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Schema;

class ProductVariant extends Model
{
    protected $fillable = [
        'product_id',
        'sku',
        'attributes',
        'price',
        'sale_price',
        'is_main_variant',
        'weight',
    ];

    protected $casts = [
        'attributes'      => 'array',
        'is_main_variant' => 'boolean',
        'price'           => 'decimal:0',
        'sale_price'      => 'decimal:0',
        'weight'          => 'integer',
    ];

    /** Quan hệ */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // 1-1 tồn kho
    public function inventory()
    {
        return $this->hasOne(Inventory::class, 'product_variant_id');
    }

    /** Cho báo cáo: dòng bán phát sinh từ biến thể */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'product_variant_id');
    }

    /** Giá hiển thị (ưu tiên sale_price nếu có) */
    public function getEffectivePriceAttribute()
    {
        return $this->sale_price && $this->sale_price > 0 ? $this->sale_price : $this->price;
    }

    /** Tự phát hiện tên cột tồn kho thật sự trên bảng inventories */
    public static function detectInventoryStockColumn(): ?string
    {
        static $col = null;
        if ($col !== null) return $col;

        foreach (['quantity', 'stock', 'qty', 'stock_quantity', 'available', 'available_quantity'] as $c) {
            if (Schema::hasColumn('inventories', $c)) {
                $col = $c;
                break;
            }
        }
        return $col;
    }

    /**
     * Scope: lọc biến thể có tồn kho thấp (<= $threshold)
     * Có thể chỉ định thủ công tên cột $column nếu muốn (vd: 'quantity')
     */
    public function scopeLowStock($q, int $threshold = 5, ?string $column = null)
    {
        $col = $column ?: static::detectInventoryStockColumn();
        if (!$col) {
            // Không tìm thấy cột tồn kho → trả về rỗng để tránh lỗi SQL
            return $q->whereRaw('1=0');
        }
        return $q->whereHas('inventory', fn($qq) => $qq->where($col, '<=', $threshold));
    }

    /** Tên hiển thị biến thể */
    public function getDisplayNameAttribute()
    {
        if ($this->is_main_variant && empty($this->attributes)) {
            return $this->sku ?? 'Main Variant';
        }

        $parts = [];
        if (is_array($this->attributes)) {
            foreach ($this->attributes as $key => $value) {
                if ($value) $parts[] = ucfirst($key) . ': ' . $value;
            }
        }

        return ($this->sku ?? 'Variant') . (count($parts) ? ' (' . implode(', ', $parts) . ')' : '');
    }
}
