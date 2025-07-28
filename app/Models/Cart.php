<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'product_variant_id',
        'quantity',
    ];

    /**
     * Lấy thông tin người dùng sở hữu giỏ hàng này.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Lấy thông tin biến thể sản phẩm trong giỏ hàng.
     */
    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }
}
