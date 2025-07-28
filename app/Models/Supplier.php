<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $fillable = [
        'name', 'contact_name', 'phone', 'email', 'address', 'is_active'
    ];
    // Thêm scope để lọc nhà cung cấp active
    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }
}
