<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->unsignedBigInteger('province_id')->nullable()->after('gender');
            $table->unsignedBigInteger('district_id')->nullable()->after('province_id');
            $table->unsignedBigInteger('ward_id')->nullable()->after('district_id');

            // nếu đã có bảng tỉnh/quận/xã, bạn có thể thêm FK:
            // $table->foreign('province_id')->references('id')->on('provinces');
            // …
        });
    }

    public function down(): void
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->dropForeign(['province_id']);
            $table->dropForeign(['district_id']);
            $table->dropForeign(['ward_id']);
            $table->dropColumn(['province_id','district_id','ward_id']);
        });
    }
};
