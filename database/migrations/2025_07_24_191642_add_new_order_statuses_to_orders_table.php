<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Bước 1: Thay đổi cột 'status' thành VARCHAR tạm thời
        DB::statement("ALTER TABLE orders ALTER COLUMN status TYPE VARCHAR(255) USING (status::VARCHAR)");

        // Bước 2: Cập nhật dữ liệu cũ để tương thích với trạng thái mới
        DB::statement("UPDATE orders SET status = 'shipped_to_shipper' WHERE status = 'shipped'");

        // Bước 3: Thay đổi cột 'status' thành NOT NULL với DEFAULT
        DB::statement("ALTER TABLE orders ALTER COLUMN status SET DEFAULT 'pending'");
        DB::statement("ALTER TABLE orders ALTER COLUMN status SET NOT NULL");

        // (Tùy chọn) Thêm constraint để giới hạn giá trị hợp lệ (thay thế ENUM)
        DB::statement("ALTER TABLE orders ADD CONSTRAINT check_status CHECK (status IN ('pending', 'processing', 'shipped_to_shipper', 'shipping', 'delivered', 'cancelled'))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Bước 1 (Rollback): Thay đổi cột 'status' thành VARCHAR tạm thời
        DB::statement("ALTER TABLE orders ALTER COLUMN status TYPE VARCHAR(255) USING (status::VARCHAR)");

        // Bước 2 (Rollback): Cập nhật dữ liệu để hoàn nguyên về trạng thái cũ
        DB::statement("UPDATE orders SET status = 'shipped' WHERE status IN ('shipped_to_shipper', 'shipping')");

        // Bước 3 (Rollback): Thay đổi cột 'status' thành NOT NULL với DEFAULT
        DB::statement("ALTER TABLE orders ALTER COLUMN status SET DEFAULT 'pending'");
        DB::statement("ALTER TABLE orders ALTER COLUMN status SET NOT NULL");

        // (Tùy chọn) Thêm constraint rollback với giá trị cũ
        DB::statement("ALTER TABLE orders ADD CONSTRAINT check_status CHECK (status IN ('pending', 'processing', 'shipped', 'delivered', 'cancelled'))");
    }
};