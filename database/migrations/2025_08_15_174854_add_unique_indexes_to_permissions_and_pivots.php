<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /** Check index exists (MySQL) */
    private function hasIndex(string $table, string $index): bool
    {
        return DB::table('information_schema.statistics')
            ->whereRaw('TABLE_SCHEMA = DATABASE()')
            ->where('TABLE_NAME', $table)
            ->where('INDEX_NAME', $index)
            ->exists();
    }

    /** Dọn dữ liệu trùng trước khi add unique (an toàn) */
    private function dedupePermissions(): void
    {
        $dups = DB::table('permissions')
            ->select('module_name', 'action', DB::raw('COUNT(*) as c'))
            ->groupBy('module_name', 'action')
            ->having('c', '>', 1)
            ->get();

        foreach ($dups as $d) {
            // Giữ lại 1 bản ghi (id nhỏ nhất), xoá các bản ghi còn lại
            $ids = DB::table('permissions')
                ->where('module_name', $d->module_name)
                ->where('action', $d->action)
                ->orderBy('id', 'asc')
                ->pluck('id')
                ->toArray();

            if (count($ids) > 1) {
                // bỏ id đầu, xoá phần còn lại
                array_shift($ids);
                DB::table('permissions')->whereIn('id', $ids)->delete();
            }
        }
    }

    public function up(): void
    {
        // ====== UNIQUE (module_name, action) cho permissions ======
        if (!$this->hasIndex('permissions', 'permissions_module_action_unique')) {
            // Nếu cần, dọn trùng để tránh lỗi UniqueConstraintViolation
            $this->dedupePermissions();

            Schema::table('permissions', function (Blueprint $t) {
                $t->unique(['module_name', 'action'], 'permissions_module_action_unique');
            });
        }

        // ====== UNIQUE (user_id, permission_id) cho permission_user ======
        if (Schema::hasTable('permission_user')
            && !$this->hasIndex('permission_user', 'permission_user_user_permission_unique')) {
            Schema::table('permission_user', function (Blueprint $t) {
                $t->unique(['user_id', 'permission_id'], 'permission_user_user_permission_unique');
            });
        }

        // ====== UNIQUE (role_id, permission_id) cho role_permission ======
        if (Schema::hasTable('role_permission')
            && !$this->hasIndex('role_permission', 'role_permission_role_permission_unique')) {
            Schema::table('role_permission', function (Blueprint $t) {
                $t->unique(['role_id', 'permission_id'], 'role_permission_role_permission_unique');
            });
        }
    }

    public function down(): void
    {
        // Drop unique của permissions nếu tồn tại
        if ($this->hasIndex('permissions', 'permissions_module_action_unique')) {
            Schema::table('permissions', function (Blueprint $t) {
                $t->dropUnique('permissions_module_action_unique');
            });
        }

        // Drop unique của permission_user nếu tồn tại
        if (Schema::hasTable('permission_user')
            && $this->hasIndex('permission_user', 'permission_user_user_permission_unique')) {
            Schema::table('permission_user', function (Blueprint $t) {
                $t->dropUnique('permission_user_user_permission_unique');
            });
        }

        // Drop unique của role_permission nếu tồn tại
        if (Schema::hasTable('role_permission')
            && $this->hasIndex('role_permission', 'role_permission_role_permission_unique')) {
            Schema::table('role_permission', function (Blueprint $t) {
                $t->dropUnique('role_permission_role_permission_unique');
            });
        }
    }
};
