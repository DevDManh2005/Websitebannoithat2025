<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    public $timestamps = false; // Bảng settings không cần timestamps
    protected $primaryKey = 'key'; // Dùng cột 'key' làm khóa chính
    public $incrementing = false; // Khóa chính không phải là số tự tăng
    protected $keyType = 'string'; // Kiểu dữ liệu của khóa chính là string

    protected $fillable = ['key', 'value'];
}