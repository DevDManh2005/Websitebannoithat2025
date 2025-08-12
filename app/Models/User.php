<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\UserProfile; 

/**
 * Class User
 *
 * @property int         $id
 * @property string      $name
 * @property string      $email
 * @property \DateTime   $email_verified_at
 * @property string      $password
 * @property bool        $is_active
 * @property string|null $remember_token
 * @property int $role_id

 */
class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active'         => 'boolean',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Các permission được gán cho staff thông qua pivot role_permission.
     */
    public function permissions()
    {
        return $this->belongsToMany(
            Permission::class,
            'role_permission',
            'role_id',
            'permission_id'
        );
    }

    /**
     * Kiểm tra xem user có permission module:action hay không
     */
    public function hasPermission(string $module, string $action): bool
    {
        return $this->permissions
            ->contains(
                fn($perm) =>
                $perm->module_name === $module
                    && $perm->action === $action
            );
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

    // Kiểm tra xem user đã mua sản phẩm này chưa
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
