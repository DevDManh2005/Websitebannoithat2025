<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void { Schema::dropIfExists('points'); }
    public function down(): void {
        Schema::create('points', function ($table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->integer('points')->default(0);
            $table->string('note', 255)->nullable();
            $table->timestamps();
        });
    }
};