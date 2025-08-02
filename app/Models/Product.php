<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'brand_id',
        'supplier_id',
        'description',
        'label',
        'is_active',
        'is_featured',
    ];

    /** Quan hệ */
    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function wishlistedBy()
    {
        return $this->belongsToMany(User::class, 'wishlists', 'product_id', 'user_id');
    }

    /** Tự động set slug khi lưu */
    protected static function booted()
    {
        static::saving(function (Product $p) {
            // Nếu chưa nhập slug, dùng từ name; nếu đã nhập slug, vẫn slugify lại
            $base = $p->slug ?: $p->name;
            $p->slug = Str::slug($base);
        });
    }

    /** Scope lấy những bản ghi active */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function inventories()
    {
        return $this->hasMany(Inventory::class);
    }

      public function reviews()
    {
        return $this->hasMany(ProductReview::class);
    }

    public function approvedReviews()
    {
        return $this->reviews()->where('status', 'approved');
    }

    public function getAverageRatingAttribute(): float
    {
        return round($this->approvedReviews()->avg('rating'), 1);
    }
}
