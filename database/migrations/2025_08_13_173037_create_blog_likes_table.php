<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Nếu bảng đã tồn tại -> (tuỳ chọn) đảm bảo unique index, rồi bỏ qua tạo bảng
        if (Schema::hasTable('blog_likes')) {
            // Đảm bảo có unique (blog_id, user_id) nếu chưa có
            $uniqueExists = DB::selectOne("
                SELECT 1
                FROM information_schema.STATISTICS
                WHERE TABLE_SCHEMA = DATABASE()
                  AND TABLE_NAME = 'blog_likes'
                  AND INDEX_NAME = 'blog_likes_blog_id_user_id_unique'
                LIMIT 1
            ");
            if (!$uniqueExists) {
                try {
                    Schema::table('blog_likes', function (Blueprint $table) {
                        $table->unique(['blog_id', 'user_id'], 'blog_likes_blog_id_user_id_unique');
                    });
                } catch (\Throwable $e) {
                    // bỏ qua nếu DB đã có unique với tên khác
                }
            }
            return;
        }

        // Tạo mới nếu chưa có
        Schema::create('blog_likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('blog_id')->constrained('blogs')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            // ngăn người dùng like trùng một bài
            $table->unique(['blog_id','user_id'], 'blog_likes_blog_id_user_id_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_likes');
    }
};
