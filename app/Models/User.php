<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = ['name', 'email', 'password', 'role_id', 'is_active'];
    protected $hidden   = ['password', 'remember_token'];
    protected $casts    = ['email_verified_at' => 'datetime', 'is_active' => 'boolean'];

    // --- Quan hệ vai trò ---
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    // --- Quyền gán trực tiếp cho user ---
    public function directPermissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'permission_user')->withTimestamps();
    }

    /**
     * Alias để tương thích các chỗ đang gọi ->permissions
     * (ví dụ: with(['permissions','role.permissions'])).
     */
    public function permissions(): BelongsToMany
    {
        return $this->directPermissions();
    }

    // --- Quyền đến từ vai trò ---
    public function rolePermissions(): BelongsToMany
    {
        // Khi user có role => dùng quan hệ permissions() của Role
        // Khi chưa có role => trả về 1 quan hệ belongsToMany "trống" (để code không lỗi).
        return $this->role
            ? $this->role->permissions()
            : $this->belongsToMany(Permission::class, 'role_permission', 'role_id', 'permission_id');
    }

    // --- Gộp tất cả quyền (trực tiếp + từ role), unique theo id ---
    public function allPermissions()
    {
        $direct = $this->directPermissions()->get();
        $fromRole = $this->role ? $this->role->permissions()->get() : collect();
        return $direct->merge($fromRole)->unique('id')->values();
    }

    // --- Kiểm tra quyền theo module + action ---
    public function hasPermission(string $module, string $action): bool
    {
        if ($this->directPermissions()
                ->where('module_name', $module)
                ->where('action', $action)
                ->exists()) {
            return true;
        }

        return $this->role
            ? $this->role->permissions()
                ->where('module_name', $module)
                ->where('action', $action)
                ->exists()
            : false;
    }

    // --- Thông tin hồ sơ ---
    public function profile(): HasOne
    {
        return $this->hasOne(UserProfile::class);
    }

    // --- Các quan hệ khác ---
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

    // --- Helpers phân nhóm ---
    public function isAdmin(): bool
    {
        return optional($this->role)->name === 'admin';
    }

    public function isStaff(): bool
    {
        return in_array(optional($this->role)->name, ['admin', 'staff'], true);
    }

    public function isCustomer(): bool
    {
        return ! $this->isStaff();
    }

    // --- Scopes ---
    public function scopeActive($q)
    {
        return $q->where('is_active', 1);
    }

    public function scopeAdmins($q)
    {
        return $q->whereHas('role', fn($r) => $r->where('name', 'admin'));
    }

    public function scopeStaffs($q)
    {
        return $q->whereHas('role', fn($r) => $r->whereIn('name', ['admin', 'staff']));
    }

    public function scopeCustomers($q)
    {
        return $q->whereDoesntHave('role', fn($r) => $r->whereIn('name', ['admin', 'staff']));
    }

    // --- Helpers khác ---
    public function hasReviewedProduct($productId)
    {
        return $this->reviews()->where('product_id', $productId)->exists();
    }

    public function hasPurchasedProduct($productId)
    {
        return $this->orders()
            ->where('status', '!=', Order::ST_CANCEL)
            ->whereHas('items.variant', fn($q) => $q->where('product_id', $productId))
            ->exists();
    }
}
