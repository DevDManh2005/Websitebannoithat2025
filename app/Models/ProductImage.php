<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductImage extends Model
{
    protected $fillable = [
        'product_id',
        'image_url',
        'is_primary',
    ];

    /**
     * Lấy quan hệ với sản phẩm.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Accessor để tạo đường dẫn ảnh đầy đủ, có thể truy cập được từ bên ngoài.
     * Tên thuộc tính ảo: image_url_path
     * Cách dùng trong view: $image->image_url_path
     */
    public function getImageUrlPathAttribute()
    {
        // Nếu đường dẫn đã là một URL đầy đủ (bắt đầu bằng http), trả về ngay lập tức.
        if (Str::startsWith($this->image_url, ['http://', 'https://'])) {
            return $this->image_url;
        }

        // Nếu file tồn tại trong thư mục public/storage, tạo đường dẫn đầy đủ.
        if ($this->image_url && Storage::disk('public')->exists($this->image_url)) {
            return asset('storage/' . $this->image_url);
        }

        // Trả về ảnh placeholder nếu không có ảnh hoặc đường dẫn bị lỗi.
        return 'https://placehold.co/300x300?text=No+Image';
    }
}
