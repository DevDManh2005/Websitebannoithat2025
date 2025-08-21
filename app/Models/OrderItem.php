<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_variant_id',
        'quantity',
        'price',
        'subtotal',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'price'    => 'decimal:0',
        'subtotal' => 'decimal:0',
    ];

    /* Quan hệ */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    // Truy cập nhanh
    public function getProductAttribute()
    {
        return optional($this->variant)->product;
    }

    public function getBrandAttribute()
    {
        return optional($this->product)->brand;
    }

    public function getCategoriesAttribute()
    {
        return optional($this->product)?->categories ?? collect();
    }

    // Tổng dòng (fallback nếu cột subtotal chưa set)
    public function getTotalAttribute()
    {
        return $this->subtotal ?? ($this->price * $this->quantity);
    }

    // Tự tính subtotal nếu thiếu
    protected static function booted()
    {
        static::saving(function (OrderItem $i) {
            if (is_null($i->subtotal)) {
                $i->subtotal = (float) $i->price * (int) $i->quantity;
            }
        });
    }
}
