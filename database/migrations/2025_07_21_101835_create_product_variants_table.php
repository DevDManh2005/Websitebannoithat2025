<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Chạy migration để tạo bảng product_variants.
     */
    public function up(): void
    {
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id()->comment('Khóa chính, tự động tăng');
            $table->foreignId('product_id')->constrained()->onDelete('cascade')->comment('Khóa ngoại liên kết với bảng products');
            $table->string('attribute_name', 255)->nullable()->comment('Tên thuộc tính (VD: Kích thước, Màu sắc)');
            $table->text('attribute_value')->nullable()->comment('Giá trị thuộc tính (VD: 120x80x75 cm, Nâu)');
            $table->boolean('is_main_variant')->default(false)->comment('Biến thể chính');
            $table->decimal('price', 12, 2)->nullable()->comment('Giá của biến thể');
            $table->decimal('sale_price', 12, 2)->nullable()->comment('Giá khuyến mãi (nếu có)');
            $table->timestamps();
        });
    }

    /**
     * Hoàn tác migration, xóa bảng product_variants.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};