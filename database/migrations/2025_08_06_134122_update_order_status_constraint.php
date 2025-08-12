<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Xoá constraint cũ nếu có
        DB::statement("ALTER TABLE orders DROP CONSTRAINT IF EXISTS orders_status_check");
        DB::statement("ALTER TABLE orders DROP CONSTRAINT IF EXISTS check_status");

        // Đổi kiểu cột status về dạng VARCHAR để dễ thao tác
        DB::statement("ALTER TABLE orders ALTER COLUMN status TYPE VARCHAR(255) USING (status::VARCHAR)");

        // Cập nhật giá trị không hợp lệ cũ (nếu có)
        DB::statement("UPDATE orders SET status = 'shipped_to_shipper' WHERE status = 'shipped'");

        // Thiết lập lại NOT NULL và DEFAULT
        DB::statement("ALTER TABLE orders ALTER COLUMN status SET DEFAULT 'pending'");
        DB::statement("ALTER TABLE orders ALTER COLUMN status SET NOT NULL");

        // Thêm lại constraint giới hạn giá trị hợp lệ (có thêm 'received')
        DB::statement("ALTER TABLE orders ADD CONSTRAINT check_status CHECK (status IN (
            'pending', 'processing', 'shipped_to_shipper', 'shipping', 'delivered', 'cancelled', 'received'
        ))");
    }

    public function down(): void
    {
        // Xoá constraint hiện tại
        DB::statement("ALTER TABLE orders DROP CONSTRAINT IF EXISTS check_status");

        // Đổi kiểu về lại VARCHAR
        DB::statement("ALTER TABLE orders ALTER COLUMN status TYPE VARCHAR(255) USING (status::VARCHAR)");

        // Cập nhật lại giá trị về 'shipped' nếu có
        DB::statement("UPDATE orders SET status = 'shipped' WHERE status IN ('shipped_to_shipper', 'shipping')");

        // Thiết lập lại default và not null
        DB::statement("ALTER TABLE orders ALTER COLUMN status SET DEFAULT 'pending'");
        DB::statement("ALTER TABLE orders ALTER COLUMN status SET NOT NULL");

        // Thêm lại constraint ban đầu (không có 'received')
        DB::statement("ALTER TABLE orders ADD CONSTRAINT check_status CHECK (status IN (
            'pending', 'processing', 'shipped', 'delivered', 'cancelled'
        ))");
    }
};
