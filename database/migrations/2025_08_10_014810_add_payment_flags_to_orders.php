<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Các cột phục vụ thanh toán online
            if (!Schema::hasColumn('orders', 'is_paid')) {
                $table->boolean('is_paid')->default(false)->after('final_amount');
            }
            if (!Schema::hasColumn('orders', 'payment_method')) {
                $table->string('payment_method')->nullable()->after('is_paid');    // ví dụ: 'vnpay', 'cod'
            }
            if (!Schema::hasColumn('orders', 'payment_status')) {
                $table->string('payment_status')->default('unpaid')->after('payment_method'); // 'paid' | 'unpaid' | 'failed'
            }
            if (!Schema::hasColumn('orders', 'paid_at')) {
                $table->timestamp('paid_at')->nullable()->after('payment_status');
            }
            if (!Schema::hasColumn('orders', 'payment_ref')) {
                $table->string('payment_ref')->nullable()->after('paid_at'); // mã GD VNPAY
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Vì chính migration này tạo ra các cột nên có thể drop trực tiếp
            $drop = [];
            foreach (['is_paid','payment_method','payment_status','paid_at','payment_ref'] as $col) {
                if (Schema::hasColumn('orders', $col)) {
                    $drop[] = $col;
                }
            }
            if (!empty($drop)) {
                $table->dropColumn($drop);
            }
        });
    }
};
