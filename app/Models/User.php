<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\UserProfile;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = ['name', 'email', 'password', 'role_id', 'is_active'];
    protected $hidden   = ['password', 'remember_token'];
    protected $casts    = ['email_verified_at' => 'datetime', 'is_active' => 'boolean'];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Quyền gán trực tiếp cho user qua pivot permission_user
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'permission_user', 'user_id', 'permission_id')
            ->withTimestamps();
        // Nếu sau này bạn thêm cột JSON 'scope' cho permission_user:
        // ->withPivot(['scope'])
    }

    /**
     * Kiểm tra quyền "module.action" theo 2 nguồn:
     *  1) gán trực tiếp cho user (permission_user)
     *  2) gán qua vai trò (role_permission)
     */
    /** Quyền gán TRỰC TIẾP cho user (permission_user) */
    public function directPermissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'permission_user')
            ->withTimestamps();
    }

    /** Quyền đến từ vai trò */
    public function rolePermissions(): BelongsToMany
    {
        // pivot role_permission (role_id, permission_id) -> join qua role_id của user
        return $this->role()
            ->first()
            ? $this->role->permissions()
            : $this->belongsToMany(Permission::class, 'role_permission', 'role_id', 'permission_id');
    }

    /** Tập quyền hợp nhất: direct + role */
    public function allPermissions()
    {
        $direct = $this->directPermissions()->get();
        $byRole = $this->role ? $this->role->permissions()->get() : collect();
        return $direct->merge($byRole)->unique('id');
    }

    /** Kiểm tra quyền */
    public function hasPermission(string $module, string $action): bool
    {
        // ưu tiên query exists để không phải load toàn bộ
        $direct = $this->directPermissions()
            ->where('module_name', $module)
            ->where('action', $action)
            ->exists();

        if ($direct) return true;

        if ($this->role) {
            return $this->role->permissions()
                ->where('module_name', $module)
                ->where('action', $action)
                ->exists();
        }

        return false;
    }

    public function profile(): HasOne
    {
        return $this->hasOne(UserProfile::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function wishlist()
    {
        return $this->belongsToMany(Product::class, 'wishlists', 'user_id', 'product_id')->withTimestamps();
    }

    public function reviews()
    {
        return $this->hasMany(ProductReview::class);
    }

    public function hasReviewedProduct($productId)
    {
        return $this->reviews()->where('product_id', $productId)->exists();
    }

    public function hasPurchasedProduct($productId)
    {
        return $this->orders()
            ->where('status', '!=', 'cancelled')
            ->whereHas('items.variant', function ($query) use ($productId) {
                $query->where('product_id', $productId);
            })->exists();
    }
}
