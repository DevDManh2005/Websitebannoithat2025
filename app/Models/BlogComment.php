<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlogComment extends Model
{
    protected $fillable = ['blog_id','user_id','parent_id','comment','is_approved'];

    public function blog() { return $this->belongsTo(Blog::class); }
    public function user() { return $this->belongsTo(User::class); }
    public function parent() { return $this->belongsTo(self::class, 'parent_id'); }
    public function children() { return $this->hasMany(self::class, 'parent_id')->where('is_approved', 1)->oldest(); }
}
