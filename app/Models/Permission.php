<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;

    protected $fillable = ['module_name', 'action'];

    public function users()
    {
        // user <-> permission: permission_user
        return $this->belongsToMany(User::class, 'permission_user', 'permission_id', 'user_id')
                    ->withTimestamps();
    }

    public function roles()
    {
        // role <-> permission: role_permission
        return $this->belongsToMany(Role::class, 'role_permission', 'permission_id', 'role_id')
                    ->withTimestamps();
    }

    // Khóa chuẩn để so quyền (vd: orders.view)
    public function getSlugAttribute(): string
    {
        return "{$this->module_name}.{$this->action}";
    }
}
