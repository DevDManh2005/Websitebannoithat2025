<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Blog extends Model
{
    protected $fillable = [
        'category_id','title','slug','excerpt','content','thumbnail',
        'seo_title','seo_description','user_id','is_published','published_at','view_count'
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    public function category() { return $this->belongsTo(BlogCategory::class, 'category_id'); }
    public function author() { return $this->belongsTo(User::class, 'user_id'); }
    public function comments() { 
        return $this->hasMany(BlogComment::class, 'blog_id')->whereNull('parent_id')->where('is_approved', 1)->latest();
    }
    public function likes() { return $this->hasMany(BlogLike::class, 'blog_id'); }

    public function scopePublished(Builder $q): Builder {
        return $q->where('is_published', 1)
                 ->when(app()->runningInConsole() === false, fn($qq) => $qq->whereNull('published_at')->orWhere('published_at','<=', now()));
    }
}
