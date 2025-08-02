<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::table('products', function (Blueprint $table) {
        // Trước tiên cần xóa foreign key
        $table->dropForeign(['category_id']);
        $table->dropColumn('category_id');
    });
}

public function down(): void
{
    Schema::table('products', function (Blueprint $table) {
        $table->foreignId('category_id')->constrained()->after('slug');
    });
}
};
