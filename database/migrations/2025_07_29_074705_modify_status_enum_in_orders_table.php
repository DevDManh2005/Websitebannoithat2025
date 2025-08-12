<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Bước 1: Chuyển cột 'status' thành VARCHAR tạm thời (nếu cần)
        DB::statement("ALTER TABLE orders ALTER COLUMN status TYPE VARCHAR(255) USING (status::VARCHAR)");

        // Bước 2: Cập nhật dữ liệu (nếu có trạng thái cũ cần chuyển đổi)
        // Ví dụ: Chuyển 'shipped' thành 'shipped_to_shipper' nếu cần
        DB::statement("UPDATE orders SET status = 'shipped_to_shipper' WHERE status = 'shipped'");

        // Bước 3: Đặt NOT NULL và DEFAULT
        DB::statement("ALTER TABLE orders ALTER COLUMN status SET DEFAULT 'pending'");
        DB::statement("ALTER TABLE orders ALTER COLUMN status SET NOT NULL");

        // Bước 4: Xóa constraint cũ nếu tồn tại, sau đó thêm constraint mới
        DB::statement("ALTER TABLE orders DROP CONSTRAINT IF EXISTS check_status");
        DB::statement("ALTER TABLE orders ADD CONSTRAINT check_status CHECK (status IN ('pending', 'processing', 'shipped_to_shipper', 'shipping', 'delivered', 'cancelled'))");
    }

    public function down(): void
    {
        // Bước 1 (Rollback): Chuyển cột 'status' thành VARCHAR tạm thời
        DB::statement("ALTER TABLE orders ALTER COLUMN status TYPE VARCHAR(255) USING (status::VARCHAR)");

        // Bước 2 (Rollback): Cập nhật dữ liệu về trạng thái cũ
        DB::statement("UPDATE orders SET status = 'shipped' WHERE status IN ('shipped_to_shipper', 'shipping')");

        // Bước 3: Đặt NOT NULL và DEFAULT
        DB::statement("ALTER TABLE orders ALTER COLUMN status SET DEFAULT 'pending'");
        DB::statement("ALTER TABLE orders ALTER COLUMN status SET NOT NULL");

        // Bước 4 (Rollback): Xóa constraint mới và thêm constraint cũ
        DB::statement("ALTER TABLE orders DROP CONSTRAINT IF EXISTS check_status");
        DB::statement("ALTER TABLE orders ADD CONSTRAINT check_status CHECK (status IN ('pending', 'processing', 'shipping', 'delivered', 'cancelled'))");
    }
};