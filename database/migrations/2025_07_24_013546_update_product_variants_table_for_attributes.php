<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateProductVariantsTableForAttributes extends Migration
{
    public function up()
    {
        Schema::table('product_variants', function (Blueprint $table) {
            // Xóa cột cũ
            $table->dropColumn(['attribute_name', 'attribute_value']);
            // Thêm cột mới
            $table->string('sku')->nullable()->after('product_id');
            $table->json('attributes')->nullable()->after('sku');
        });
    }

    public function down()
    {
        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropColumn(['sku', 'attributes']);
            $table->string('attribute_name')->nullable();
            $table->string('attribute_value')->nullable();
        });
    }
}