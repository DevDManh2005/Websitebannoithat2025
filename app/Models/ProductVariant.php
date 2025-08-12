<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
        'attributes' => 'array',
        'is_main_variant' => 'boolean',
        'price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'weight' => 'integer',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * SỬA LẠI QUAN HỆ: Chuyển từ hasMany 'inventories' sang hasOne 'inventory'.
     * Mỗi biến thể chỉ có một bản ghi tồn kho.
     */
    public function inventory()
    {
        return $this->hasOne(Inventory::class, 'product_variant_id');
    }

    public function getDisplayNameAttribute()
    {
        if ($this->is_main_variant && empty($this->attributes)) {
            return $this->sku ?? 'Main Variant';
        }

        $parts = [];
        if (!empty($this->attributes) && is_array($this->attributes)) {
            foreach ($this->attributes as $key => $value) {
                if ($value) {
                    $parts[] = ucfirst($key) . ': ' . $value;
                }
            }
        }

        return ($this->sku ?? 'Variant') . (count($parts) > 0 ? ' (' . implode(', ', $parts) . ')' : '');
    }
}
