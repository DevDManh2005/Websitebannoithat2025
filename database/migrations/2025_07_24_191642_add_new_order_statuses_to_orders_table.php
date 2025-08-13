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
        // Dùng Query Builder để cập nhật dữ liệu (tương thích mọi DB)
        DB::table('orders')
            ->where('status', 'shipped')
            ->update(['status' => 'shipped_to_shipper']);

        // Dùng Schema Builder để thay đổi thuộc tính cột (tương thích mọi DB)
        Schema::table('orders', function (Blueprint $table) {
            $table->string('status', 255)->default('pending')->nullable(false)->change();
        });
        
        // CHECK constraint thường phải dùng SQL thuần, nhưng ta có thể làm nó an toàn hơn
        // Bằng cách xóa constraint cũ (nếu có) trước khi tạo cái mới.
        // Tên 'orders_status_check' là tên constraint chuẩn do Laravel tạo ra.
        DB::statement('ALTER TABLE orders DROP CONSTRAINT IF EXISTS orders_status_check');
        DB::statement("ALTER TABLE orders ADD CONSTRAINT orders_status_check CHECK (status IN ('pending', 'processing', 'shipped_to_shipper', 'shipping', 'delivered', 'cancelled'))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Hoàn tác việc cập nhật dữ liệu
        DB::table('orders')
            ->whereIn('status', ['shipped_to_shipper', 'shipping'])
            ->update(['status' => 'shipped']);
        
        // Xóa constraint mới và thêm lại constraint cũ
        DB::statement('ALTER TABLE orders DROP CONSTRAINT IF EXISTS orders_status_check');
        DB::statement("ALTER TABLE orders ADD CONSTRAINT orders_status_check CHECK (status IN ('pending', 'processing', 'shipped', 'delivered', 'cancelled'))");
    }
};