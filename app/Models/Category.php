<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'parent_id',
        'is_active',
        'position',
        'image', // Thêm 'image' vào đây
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'position' => 'integer',
    ];

    /**
     * Lấy danh mục cha của danh mục hiện tại.
     */
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * Lấy tất cả các danh mục con trực tiếp.
     */
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }
    
    /**
     * Lấy tất cả các sản phẩm thuộc về danh mục này.
     * Đây là quan hệ nhiều-nhiều mới.
     */
    public function products()
    {
        return $this->belongsToMany(Product::class);
    }
    
    /**
     * Lấy các danh mục đang hoạt động.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }

    /**
     * Boot the model.
     * Tự động tạo slug khi tên thay đổi.
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($category) {
            $category->slug = Str::slug($category->name);
        });
    }
}