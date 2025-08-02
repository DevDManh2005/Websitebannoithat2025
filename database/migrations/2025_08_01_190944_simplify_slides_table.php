<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Xóa bảng slide_images nếu nó tồn tại
        Schema::dropIfExists('slide_images');

        // Thêm lại các cột cần thiết vào bảng slides
        Schema::table('slides', function (Blueprint $table) {
            if (!Schema::hasColumn('slides', 'title')) {
                $table->string('title')->after('id');
            }
            if (!Schema::hasColumn('slides', 'subtitle')) {
                $table->text('subtitle')->nullable()->after('title');
            }
            if (!Schema::hasColumn('slides', 'image')) {
                $table->string('image')->after('subtitle');
            }
            if (!Schema::hasColumn('slides', 'button_text')) {
                $table->string('button_text')->nullable()->after('image');
            }
            if (!Schema::hasColumn('slides', 'button_link')) {
                $table->string('button_link')->nullable()->after('button_text');
            }
        });
    }

    public function down(): void
    {
        // Logic để rollback nếu cần
        Schema::create('slide_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('slide_id')->constrained()->onDelete('cascade');
            $table->string('image');
            $table->string('title');
            $table->text('subtitle')->nullable();
            $table->string('button_text')->nullable();
            $table->string('button_link')->nullable();
            $table->timestamps();
        });

        Schema::table('slides', function (Blueprint $table) {
            $table->dropColumn(['title', 'subtitle', 'image', 'button_text', 'button_link']);
        });
    }
};