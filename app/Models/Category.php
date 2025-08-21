<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name','slug','parent_id','is_active','position','image'];
    protected $casts = ['is_active'=>'boolean','position'=>'integer'];

    public function parent()   { return $this->belongsTo(Category::class, 'parent_id'); }
    public function children() { return $this->hasMany(Category::class, 'parent_id'); }
    public function products() { return $this->belongsToMany(Product::class); }

    public function scopeActive($q)   { return $q->where('is_active', 1); }
    public function scopeRoots($q)    { return $q->whereNull('parent_id'); }
    public function scopeLeaves($q)   { return $q->whereNotNull('parent_id'); }

    protected static function boot()
    {
        parent::boot();
        static::saving(function ($c) { $c->slug = Str::slug($c->name); });
    }
}
