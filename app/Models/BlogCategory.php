<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BlogCategory extends Model
{
    protected $fillable = ['name','slug','parent_id','description','thumbnail','is_active','sort_order'];

    public function parent() { return $this->belongsTo(self::class, 'parent_id'); }
    public function children(): HasMany { return $this->hasMany(self::class, 'parent_id'); }
    public function blogs(): HasMany { return $this->hasMany(Blog::class, 'category_id'); }
}
