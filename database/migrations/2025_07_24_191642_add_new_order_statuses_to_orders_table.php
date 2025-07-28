<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // Import DB facade

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Bước 1: Thay đổi cột 'status' thành VARCHAR tạm thời
        // Điều này giúp loại bỏ ràng buộc ENUM cũ và cho phép cập nhật dữ liệu
        DB::statement("ALTER TABLE orders MODIFY status VARCHAR(255) NOT NULL DEFAULT 'pending'");

        // Bước 2: Cập nhật dữ liệu cũ để tương thích với ENUM mới
        // Chuyển tất cả các đơn hàng có trạng thái 'shipped' cũ thành 'shipped_to_shipper'
        // Nếu có các trạng thái cũ khác cần chuyển đổi, hãy thêm các câu lệnh UPDATE tương ứng ở đây.
        DB::statement("UPDATE orders SET status = 'shipped_to_shipper' WHERE status = 'shipped'");

        // Bước 3: Thay đổi cột 'status' trở lại ENUM với các giá trị mới
        DB::statement("ALTER TABLE orders MODIFY status ENUM('pending', 'processing', 'shipped_to_shipper', 'shipping', 'delivered', 'cancelled') NOT NULL DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Bước 1 (Rollback): Thay đổi cột 'status' thành VARCHAR tạm thời
        DB::statement("ALTER TABLE orders MODIFY status VARCHAR(255) NOT NULL DEFAULT 'pending'");

        // Bước 2 (Rollback): Cập nhật dữ liệu để hoàn nguyên về ENUM cũ
        // Chuyển 'shipped_to_shipper' và 'shipping' trở lại 'shipped' cũ
        DB::statement("UPDATE orders SET status = 'shipped' WHERE status IN ('shipped_to_shipper', 'shipping')");

        // Bước 3 (Rollback): Thay đổi cột 'status' trở lại ENUM cũ
        DB::statement("ALTER TABLE orders MODIFY status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') NOT NULL DEFAULT 'pending'");
    }
};