<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ProductReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
        'rating',
        'review',
        'image',
        'status',
    ];

    /**
     * Ép kiểu dữ liệu để đảm bảo user_id và product_id luôn là số nguyên.
     * Giúp giải quyết lỗi so sánh === trong Blade.
     */
    protected $casts = [
        'user_id' => 'integer',
        'product_id' => 'integer',
        'rating' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Accessor: Tự động chuyển đổi chuỗi đường dẫn ảnh trong DB
     * thành một mảng các URL đầy đủ để sử dụng trong view.
     * Cách dùng trong Blade: $review->images
     */
    public function images(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (empty($this->image)) {
                    return [];
                }

                $paths = explode(',', $this->image);
                $urls = [];
                foreach ($paths as $path) {
                    if(trim($path)) {
                        $urls[] = Storage::url($path);
                    }
                }
                return $urls;
            }
        );
    }
}