<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Thêm từng cột nếu thiếu
        Schema::table('blogs', function (Blueprint $table) {
            if (!Schema::hasColumn('blogs', 'category_id')) {
                $table->foreignId('category_id')->nullable()->after('id');
            }
            if (!Schema::hasColumn('blogs', 'thumbnail')) {
                $table->string('thumbnail')->nullable()->after('excerpt');
            }
            if (!Schema::hasColumn('blogs', 'seo_title')) {
                $table->string('seo_title')->nullable()->after('thumbnail');
            }
            if (!Schema::hasColumn('blogs', 'seo_description')) {
                $table->string('seo_description')->nullable()->after('seo_title');
            }
            if (!Schema::hasColumn('blogs', 'published_at')) {
                $table->timestamp('published_at')->nullable()->after('is_published');
            }
            if (!Schema::hasColumn('blogs', 'view_count')) {
                $table->unsignedBigInteger('view_count')->default(0)->after('published_at');
            }
        });

        // Tạo FK cho category_id nếu CHƯA có ràng buộc
        if (Schema::hasColumn('blogs', 'category_id')) {
            $fkExists = DB::selectOne("
                SELECT 1
                FROM information_schema.KEY_COLUMN_USAGE
                WHERE TABLE_SCHEMA = DATABASE()
                  AND TABLE_NAME = 'blogs'
                  AND COLUMN_NAME = 'category_id'
                  AND REFERENCED_TABLE_NAME IS NOT NULL
                LIMIT 1
            ");

            if (!$fkExists) {
                Schema::table('blogs', function (Blueprint $table) {
                    $table->foreign('category_id', 'blogs_category_id_foreign')
                          ->references('id')->on('blog_categories')
                          ->nullOnDelete();
                });
            }
        }
    }

    public function down(): void
    {
        // Hạ vá: chỉ xóa nếu tồn tại
        Schema::table('blogs', function (Blueprint $table) {
            // cố gắng drop FK nếu có
            try { $table->dropForeign('blogs_category_id_foreign'); } catch (\Throwable $e) {}
            if (Schema::hasColumn('blogs', 'category_id'))   $table->dropColumn('category_id');
            if (Schema::hasColumn('blogs', 'thumbnail'))     $table->dropColumn('thumbnail');
            if (Schema::hasColumn('blogs', 'seo_title'))     $table->dropColumn('seo_title');
            if (Schema::hasColumn('blogs', 'seo_description')) $table->dropColumn('seo_description');
            if (Schema::hasColumn('blogs', 'published_at'))  $table->dropColumn('published_at');
            if (Schema::hasColumn('blogs', 'view_count'))    $table->dropColumn('view_count');
        });
    }
};
