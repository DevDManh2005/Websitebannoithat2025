<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('blogs', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable()
                  ->after('id')->constrained('blog_categories')->nullOnDelete();

            $table->string('thumbnail')->nullable()->after('excerpt');
            $table->string('seo_title')->nullable()->after('thumbnail');
            $table->string('seo_description')->nullable()->after('seo_title');
            $table->timestamp('published_at')->nullable()->after('is_published');
            $table->unsignedBigInteger('view_count')->default(0)->after('published_at');
        });
    }
    public function down(): void {
        Schema::table('blogs', function (Blueprint $table) {
            $table->dropConstrainedForeignId('category_id');
            $table->dropColumn(['thumbnail','seo_title','seo_description','published_at','view_count']);
        });
    }
};
