<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('blog_comments')) return;

        // Thêm cột nếu CHƯA có
        if (!Schema::hasColumn('blog_comments', 'parent_id')) {
            Schema::table('blog_comments', function (Blueprint $table) {
                // parent comment (cùng bảng)
                $table->foreignId('parent_id')->nullable()->after('user_id');
            });
        }

        // Thêm FK nếu CHƯA có
        if (Schema::hasColumn('blog_comments', 'parent_id')) {
            $fkExists = DB::selectOne("
                SELECT 1
                FROM information_schema.KEY_COLUMN_USAGE
                WHERE TABLE_SCHEMA = DATABASE()
                  AND TABLE_NAME = 'blog_comments'
                  AND COLUMN_NAME = 'parent_id'
                  AND REFERENCED_TABLE_NAME IS NOT NULL
                LIMIT 1
            ");

            if (!$fkExists) {
                try {
                    Schema::table('blog_comments', function (Blueprint $table) {
                        // set null khi xóa comment cha
                        $table->foreign('parent_id', 'blog_comments_parent_id_foreign')
                              ->references('id')->on('blog_comments')
                              ->nullOnDelete();
                    });
                } catch (\Throwable $e) {
                    // bỏ qua nếu hệ quản trị DB đã tạo FK với tên khác
                }
            }
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('blog_comments')) return;

        // drop FK nếu tồn tại (không vỡ nếu tên khác)
        try {
            Schema::table('blog_comments', function (Blueprint $table) {
                $table->dropForeign('blog_comments_parent_id_foreign');
            });
        } catch (\Throwable $e) {}

        // drop cột nếu đang có
        Schema::table('blog_comments', function (Blueprint $table) {
            if (Schema::hasColumn('blog_comments', 'parent_id')) {
                $table->dropColumn('parent_id');
            }
        });
    }
};
