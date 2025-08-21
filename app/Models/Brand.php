<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Brand extends Model
{
    protected $fillable = ['name','slug','logo','is_active'];

    public function scopeActive($q){ return $q->where('is_active', 1); }

    protected static function booted()
    {
        static::saving(function (Brand $b) { $b->slug = Str::slug($b->name); });
    }

    public function getLogoUrlAttribute(): ?string
    {
        if (!$this->logo) return null;
        if (Str::startsWith($this->logo, ['http://','https://'])) return $this->logo;
        return asset('storage/'.$this->logo);
    }
}
