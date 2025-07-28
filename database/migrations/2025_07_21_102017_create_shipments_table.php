<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->string('receiver_name');
            $table->string('phone');
            $table->string('address');
            $table->string('city')->nullable();
            $table->string('district')->nullable();
            $table->string('ward')->nullable();
            $table->decimal('shipping_fee', 12, 2)->default(0);
            $table->string('tracking_code')->nullable();
            $table->enum('status', ['waiting', 'shipping', 'delivered', 'failed'])->default('waiting');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('shipments');
    }
};
