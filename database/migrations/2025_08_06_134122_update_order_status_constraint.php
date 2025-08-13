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
        // Xóa constraint cũ (nếu có) một cách an toàn
        DB::statement('ALTER TABLE orders DROP CONSTRAINT IF EXISTS orders_status_check');
        DB::statement('ALTER TABLE orders DROP CONSTRAINT IF EXISTS check_status');

        // Cập nhật dữ liệu bằng Query Builder
        DB::table('orders')
            ->where('status', 'shipped')
            ->update(['status' => 'shipped_to_shipper']);

        // Thay đổi cột bằng Schema Builder (yêu cầu doctrine/dbal)
        Schema::table('orders', function (Blueprint $table) {
            $table->string('status')->default('pending')->nullable(false)->change();
        });

        // Thêm lại constraint mới
        DB::statement("ALTER TABLE orders ADD CONSTRAINT orders_status_check CHECK (status IN ('pending', 'processing', 'shipped_to_shipper', 'shipping', 'delivered', 'cancelled', 'received'))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Xóa constraint hiện tại
        DB::statement('ALTER TABLE orders DROP CONSTRAINT IF EXISTS orders_status_check');
        DB::statement('ALTER TABLE orders DROP CONSTRAINT IF EXISTS check_status');

        // Hoàn tác dữ liệu bằng Query Builder
        DB::table('orders')
            ->whereIn('status', ['shipped_to_shipper', 'shipping'])
            ->update(['status' => 'shipped']);

        // Thêm lại constraint cũ
        DB::statement("ALTER TABLE orders ADD CONSTRAINT orders_status_check CHECK (status IN ('pending', 'processing', 'shipped', 'delivered', 'cancelled'))");
    }
};