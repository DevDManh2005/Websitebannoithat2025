<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    protected $fillable = [
        'name','slug','brand_id','supplier_id','description','label','is_active','is_featured',
    ];

    /** Quan hệ */
    public function categories() { return $this->belongsToMany(Category::class); }
    public function brand()      { return $this->belongsTo(Brand::class); }
    public function supplier()   { return $this->belongsTo(Supplier::class); }
    public function variants()   { return $this->hasMany(ProductVariant::class); }
    public function images()     { return $this->hasMany(ProductImage::class); }

    public function primaryImage()
    {
        return $this->hasOne(ProductImage::class)
            ->where('is_primary', true)
            ->withDefault(function ($img, $product) {
                $first = $product->images()->first();
                if ($first) $img->setRawAttributes($first->getAttributes());
            });
    }

    public function wishlistedBy()
    {
        return $this->belongsToMany(User::class, 'wishlists', 'product_id', 'user_id');
    }

    protected static function booted()
    {
        static::saving(function (Product $p) {
            $base = $p->slug ?: $p->name;
            $p->slug = Str::slug($base);
        });
    }

    public function scopeActive($q) { return $q->where('is_active', 1); }

    public function inventories()  { return $this->hasMany(Inventory::class); }
    public function reviews()      { return $this->hasMany(ProductReview::class); }
    public function approvedReviews() { return $this->reviews()->where('status','approved'); }
    public function getAverageRatingAttribute(): float
    {
        return round($this->approvedReviews()->avg('rating') ?? 0, 1);
    }

    // Dòng bán qua OrderItem
    public function orderItems()
    {
        // parent(Product) -> intermediate(ProductVariant.product_id) -> final(OrderItem.product_variant_id)
        return $this->hasManyThrough(
            OrderItem::class,
            ProductVariant::class,
            'product_id',          // FK trên variants trỏ về products
            'product_variant_id',  // FK trên order_items trỏ về variants
            'id',                  // local key on products
            'id'                   // local key on variants
        );
    }

    // Ảnh cover dùng nhanh trong report
    public function getCoverImageUrlAttribute(): ?string
    {
        $img = $this->primaryImage ?: $this->images()->first();
        return $img ? $img->image_url_path : null;
    }
}
