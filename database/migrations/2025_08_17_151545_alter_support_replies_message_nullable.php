<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('support_replies', function (Blueprint $table) {
            // Cho phép null cho message (và attachment nếu bạn muốn)
            $table->text('message')->nullable()->change();
            $table->string('attachment')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('support_replies', function (Blueprint $table) {
            // Nếu cần rollback (không bắt buộc phải khôi phục NOT NULL)
            $table->text('message')->nullable(false)->change();
            $table->string('attachment')->nullable(false)->change();
        });
    }
};
