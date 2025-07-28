<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('module_name');
            $table->string('action'); // view, create, update, delete
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('permissions');
    }
};
