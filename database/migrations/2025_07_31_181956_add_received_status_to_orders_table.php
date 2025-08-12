<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Bước 1: Chuyển cột 'status' thành VARCHAR tạm thời
        DB::statement("ALTER TABLE orders ALTER COLUMN status TYPE VARCHAR(255) USING (status::VARCHAR)");

        // Bước 2: Cập nhật dữ liệu (nếu có trạng thái cũ cần chuyển đổi)
        // Ví dụ: Không cần chuyển đổi nếu 'received' là trạng thái mới

        // Bước 3: Đặt NOT NULL và DEFAULT
        DB::statement("ALTER TABLE orders ALTER COLUMN status SET DEFAULT 'pending'");
        DB::statement("ALTER TABLE orders ALTER COLUMN status SET NOT NULL");

        // Bước 4: Xóa constraint cũ nếu tồn tại, sau đó thêm constraint mới với 'received'
        DB::statement("ALTER TABLE orders DROP CONSTRAINT IF EXISTS check_status");
        DB::statement("ALTER TABLE orders ADD CONSTRAINT check_status CHECK (status IN ('pending', 'processing', 'shipped_to_shipper', 'shipping', 'delivered', 'received', 'cancelled'))");
    }

    public function down(): void
    {
        // Bước 1 (Rollback): Chuyển cột 'status' thành VARCHAR tạm thời
        DB::statement("ALTER TABLE orders ALTER COLUMN status TYPE VARCHAR(255) USING (status::VARCHAR)");

        // Bước 2 (Rollback): Đặt NOT NULL và DEFAULT
        DB::statement("ALTER TABLE orders ALTER COLUMN status SET DEFAULT 'pending'");
        DB::statement("ALTER TABLE orders ALTER COLUMN status SET NOT NULL");

        // Bước 3 (Rollback): Xóa constraint mới và thêm constraint cũ (không có 'received')
        DB::statement("ALTER TABLE orders DROP CONSTRAINT IF EXISTS check_status");
        DB::statement("ALTER TABLE orders ADD CONSTRAINT check_status CHECK (status IN ('pending', 'processing', 'shipped_to_shipper', 'shipping', 'delivered', 'cancelled'))");
    }
};