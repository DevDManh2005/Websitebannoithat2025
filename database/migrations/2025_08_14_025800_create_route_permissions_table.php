<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Nếu bảng đã tồn tại → chỉ bổ sung cột / index còn thiếu
        if (Schema::hasTable('route_permissions')) {

            // Bổ sung cột còn thiếu
            Schema::table('route_permissions', function (Blueprint $t) {
                if (!Schema::hasColumn('route_permissions', 'route_name'))   $t->string('route_name')->after('id');
                if (!Schema::hasColumn('route_permissions', 'module_name'))  $t->string('module_name')->after('route_name');
                if (!Schema::hasColumn('route_permissions', 'action'))       $t->string('action')->after('module_name');
                if (!Schema::hasColumn('route_permissions', 'area'))         $t->string('area')->default('staff')->after('action');
                if (!Schema::hasColumn('route_permissions', 'is_active'))    $t->boolean('is_active')->default(true)->after('area');
                if (!Schema::hasColumn('route_permissions', 'created_at'))   $t->timestamp('created_at')->nullable()->after('is_active');
                if (!Schema::hasColumn('route_permissions', 'updated_at'))   $t->timestamp('updated_at')->nullable()->after('created_at');
            });

            // Đảm bảo unique(route_name)
            $hasUnique = DB::table('information_schema.STATISTICS')
                ->whereRaw('TABLE_SCHEMA = DATABASE()')
                ->where('TABLE_NAME', 'route_permissions')
                ->where('INDEX_NAME', 'route_permissions_route_name_unique')
                ->exists();

            if (!$hasUnique) {
                try {
                    Schema::table('route_permissions', function (Blueprint $t) {
                        $t->unique('route_name', 'route_permissions_route_name_unique');
                    });
                } catch (\Throwable $e) {
                    // bỏ qua nếu DB đã có unique tương tự với tên khác
                }
            }

            // (khuyến nghị) index cho tra cứu module+action+area
            $hasComboIdx = DB::table('information_schema.STATISTICS')
                ->whereRaw('TABLE_SCHEMA = DATABASE()')
                ->where('TABLE_NAME', 'route_permissions')
                ->where('INDEX_NAME', 'route_perm_mod_action_area_idx')
                ->exists();

            if (!$hasComboIdx) {
                try {
                    Schema::table('route_permissions', function (Blueprint $t) {
                        $t->index(['module_name', 'action', 'area'], 'route_perm_mod_action_area_idx');
                    });
                } catch (\Throwable $e) {}
            }

            return; // đã xử lý xong trường hợp bảng tồn tại
        }

        // Chưa có bảng → tạo mới
        Schema::create('route_permissions', function (Blueprint $t) {
            $t->id();
            $t->string('route_name')->unique();     // ví dụ: staff.orders.index
            $t->string('module_name');              // ví dụ: orders
            $t->string('action');                   // view|create|update|delete|...
            $t->string('area')->default('staff');   // admin|staff
            $t->boolean('is_active')->default(true);
            $t->timestamps();

            $t->index(['module_name','action','area'], 'route_perm_mod_action_area_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('route_permissions');
    }
};
