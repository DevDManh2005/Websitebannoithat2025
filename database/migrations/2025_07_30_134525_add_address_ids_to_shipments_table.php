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
        Schema::table('shipments', function (Blueprint $table) {
            // Thêm cột để lưu ID Quận/Huyện của GHN
            $table->integer('district_id')->unsigned()->nullable()->after('district');

            // Thêm cột để lưu Mã Phường/Xã của GHN
            $table->string('ward_code')->nullable()->after('ward');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->dropColumn(['district_id', 'ward_code']);
        });
    }
};