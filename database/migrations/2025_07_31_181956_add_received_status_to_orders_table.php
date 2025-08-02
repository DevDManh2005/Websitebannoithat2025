<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Thêm 'received' vào danh sách enum
        DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM('pending','processing','shipped_to_shipper','shipping','delivered','received','cancelled') NOT NULL DEFAULT 'pending'");
    }

    public function down(): void
    {
        // Quay lại trạng thái cũ nếu rollback
        DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM('pending','processing','shipped_to_shipper','shipping','delivered','cancelled') NOT NULL DEFAULT 'pending'");
    }
};