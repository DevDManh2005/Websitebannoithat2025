<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Brand extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'logo',
        'is_active',
    ];

    /**
     * Tự động set slug trước khi lưu
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }
    protected static function booted()
    {
        static::saving(function (Brand $b) {
            $b->slug = Str::slug($b->name);
        });
    }

    /**
     * URL đầy đủ đến file logo (hoặc null nếu không có)
     */
    public function getLogoUrlAttribute(): ?string
    {
        if (! $this->logo) {
            return null;
        }

        // Nếu logo đã là URL ngoài
        if (Str::startsWith($this->logo, ['http://', 'https://'])) {
            return $this->logo;
        }

        // Ngược lại, trả về đường dẫn trong storage
        return asset('storage/' . $this->logo);
    }
}
