<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
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
    // ✅ Tên hiển thị: “Thêm Sản phẩm”, “Xem Đơn hàng”, …
    public function getDisplayNameAttribute(): string
    {
        $moduleLabel = config('staff_modules')[$this->module_name]['label']
            ?? Str::headline(str_replace(['_','-'], ' ', $this->module_name));

        $map = [
            'view'          => 'Xem',
            'create'        => 'Thêm',
            'update'        => 'Cập nhật',
            'delete'        => 'Xóa',
            'ready_to_ship' => 'Sẵn sàng giao',
            'cod_paid'      => 'Đã thu COD',
            'moderate'      => 'Duyệt',
            'approve'       => 'Phê duyệt',
            'toggle'        => 'Chuyển trạng thái',
        ];

        $actionLabel = $map[$this->action] ?? Str::headline(str_replace('_',' ', $this->action));
        return trim($actionLabel.' '.$moduleLabel);
    }
}
