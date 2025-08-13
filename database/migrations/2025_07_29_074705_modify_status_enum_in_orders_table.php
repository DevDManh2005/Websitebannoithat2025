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
        // Bước 1: Cập nhật dữ liệu bằng Query Builder (tương thích mọi DB)
        DB::table('orders')
            ->where('status', 'shipped')
            ->update(['status' => 'shipped_to_shipper']);

        // Bước 2: Dùng Schema Builder để thay đổi thuộc tính cột (tương thích mọi DB)
        Schema::table('orders', function (Blueprint $table) {
            // Lệnh `change()` sẽ tự động tạo cú pháp đúng cho MySQL hoặc PostgreSQL
            $table->string('status')->default('pending')->nullable(false)->change();
        });

        // Bước 3: Cập nhật CHECK constraint một cách an toàn
        DB::statement('ALTER TABLE orders DROP CONSTRAINT IF EXISTS check_status');
        DB::statement("ALTER TABLE orders ADD CONSTRAINT check_status CHECK (status IN ('pending', 'processing', 'shipped_to_shipper', 'shipping', 'delivered', 'cancelled'))");
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

        // Hoàn tác CHECK constraint
        DB::statement('ALTER TABLE orders DROP CONSTRAINT IF EXISTS check_status');
        DB::statement("ALTER TABLE orders ADD CONSTRAINT check_status CHECK (status IN ('pending', 'processing', 'shipped', 'delivered', 'cancelled'))");
    }
};