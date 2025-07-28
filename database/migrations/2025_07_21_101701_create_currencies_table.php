<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // VND, USD, EUR...
            $table->string('symbol');         // ₫, $, €
            $table->decimal('exchange_rate', 15, 4)->default(1);
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('currencies');
    }
};
