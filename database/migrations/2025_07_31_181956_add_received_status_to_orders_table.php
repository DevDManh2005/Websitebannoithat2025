<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Dùng Schema Builder để thay đổi thuộc tính cột (tương thích mọi DB)
        Schema::table('orders', function (Blueprint $table) {
            $table->string('status')->default('pending')->nullable(false)->change();
        });

        // Cập nhật CHECK constraint một cách an toàn
        // Xóa constraint cũ (nếu có) để tránh lỗi
        DB::statement('ALTER TABLE orders DROP CONSTRAINT IF EXISTS orders_status_check');
        // Thêm constraint mới với trạng thái 'received'
        DB::statement("ALTER TABLE orders ADD CONSTRAINT orders_status_check CHECK (status IN ('pending', 'processing', 'shipped_to_shipper', 'shipping', 'delivered', 'received', 'cancelled'))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Hoàn tác CHECK constraint
        DB::statement('ALTER TABLE orders DROP CONSTRAINT IF EXISTS orders_status_check');
        // Thêm lại constraint cũ (không có 'received')
        DB::statement("ALTER TABLE orders ADD CONSTRAINT orders_status_check CHECK (status IN ('pending', 'processing', 'shipped_to_shipper', 'shipping', 'delivered', 'cancelled'))");
    }
};