<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // Import DB facade

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Change columns in 'locations' table
        Schema::table('locations', function (Blueprint $table) {
            // Temporarily drop foreign key constraints if they exist
            // Check if foreign key exists before dropping to avoid errors
            if (Schema::hasColumn('locations', 'city_id')) {
                // You might need to manually check and drop foreign keys if they were named differently
                // For example: $table->dropForeign(['city_id']);
            }
            if (Schema::hasColumn('locations', 'district_id')) {
                // $table->dropForeign(['district_id']);
            }
            if (Schema::hasColumn('locations', 'ward_id')) {
                // $table->dropForeign(['ward_id']);
            }

            // Change column types to string
            $table->string('city_id', 255)->nullable()->change();
            $table->string('district_id', 255)->nullable()->change();
            $table->string('ward_id', 255)->nullable()->change();
        });

        // Change columns in 'user_profiles' table
        Schema::table('user_profiles', function (Blueprint $table) {
            // Temporarily drop foreign key constraints if they exist
            // $table->dropForeign(['province_id']); // Assuming these are not foreign keys to other tables
            // $table->dropForeign(['district_id']);
            // $table->dropForeign(['ward_id']);

            // Change column types to string
            $table->string('province_id', 255)->nullable()->change();
            $table->string('district_id', 255)->nullable()->change();
            $table->string('ward_id', 255)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert columns in 'locations' table
        Schema::table('locations', function (Blueprint $table) {
            // Revert column types to unsigned big integer
            // Note: This assumes original data fits into bigint.
            // If you had actual string data, this might fail.
            $table->bigInteger('city_id')->unsigned()->nullable()->change();
            $table->bigInteger('district_id')->unsigned()->nullable()->change();
            $table->bigInteger('ward_id')->unsigned()->nullable()->change();

            // Re-add foreign key constraints if they existed originally
            // $table->foreign('city_id')->references('id')->on('cities_table_name')->onDelete('set null');
            // ...
        });

        // Revert columns in 'user_profiles' table
        Schema::table('user_profiles', function (Blueprint $table) {
            // Revert column types to unsigned big integer
            $table->bigInteger('province_id')->unsigned()->nullable()->change();
            $table->bigInteger('district_id')->unsigned()->nullable()->change();
            $table->bigInteger('ward_id')->unsigned()->nullable()->change();

            // Re-add foreign key constraints if they existed originally
            // $table->foreign('province_id')->references('id')->on('provinces_table_name')->onDelete('set null');
            // ...
        });
    }
};
