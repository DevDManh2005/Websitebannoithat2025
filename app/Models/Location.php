<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'city_id', // Thay đổi: sẽ lưu string code từ API
        'city_name',
        'district_id', // Thay đổi: sẽ lưu string code từ API
        'district_name',
        'ward_id', // Thay đổi: sẽ lưu string code từ API
        'ward_name',
    ];

    // Thêm casts để Laravel tự động ép kiểu khi truy vấn
    protected $casts = [
        'city_id' => 'string',
        'district_id' => 'string',
        'ward_id' => 'string',
    ];

    /**
     * Relationships
     */
    public function inventories()
    {
        return $this->hasMany(Inventory::class);
    }
}
