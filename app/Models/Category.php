<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Class Category
 *
 * @package App\Models
 *
 * @property int                              $id
 * @property string                           $name
 * @property string                           $slug
 * @property int|null                         $parent_id
 * @property bool                             $is_active
 * @property int                              $position
 * @property \Illuminate\Support\Carbon|null  $created_at
 * @property \Illuminate\Support\Carbon|null  $updated_at
 */
class Category extends Model
{
    // Chỉ cho phép tên, parent, trạng thái và vị trí mass-assignment
    protected $fillable = [
        'name',
        'parent_id',
        'is_active',
        'position',
    ];

    // Giá trị mặc định nếu không cung cấp
    protected $attributes = [
        'is_active' => true,
        'position'  => 0,
    ];

    // Ép kiểu cho đúng
    protected $casts = [
        'is_active' => 'boolean',
        'position'  => 'integer',
    ];

    /** Parent category */
    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /** Child categories */
    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }
    /**
     * Gán slug tự động:
     * - Khi tạo mới (creating)
     * - Khi cập nhật và name có thay đổi (updating)
     */
     public function products()
    {
        return $this->hasMany(Product::class);
    }
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            $category->slug = Str::slug($category->name);
        });

        static::updating(function ($category) {
            if ($category->isDirty('name')) {
                $category->slug = Str::slug($category->name);
            }
        });
    }
}
