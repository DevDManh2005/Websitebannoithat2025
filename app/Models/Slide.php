<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Slide extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'subtitle', 'image', 'button_text', 'button_link', 'position', 'is_active'
    ];

    // Xóa quan hệ images() không cần thiết
}