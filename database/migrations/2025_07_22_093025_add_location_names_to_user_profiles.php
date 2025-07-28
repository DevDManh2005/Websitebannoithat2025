<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('user_profiles', function (Blueprint $table) {
        $table->string('province_name')->nullable()->after('province_id');
        $table->string('district_name')->nullable()->after('district_id');
        $table->string('ward_name')->nullable()->after('ward_id');
    });
}

public function down()
{
    Schema::table('user_profiles', function (Blueprint $table) {
        $table->dropColumn(['province_name','district_name','ward_name']);
    });
}

};
