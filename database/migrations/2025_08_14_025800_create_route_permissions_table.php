<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('route_permissions', function (Blueprint $t) {
            $t->id();
            $t->string('route_name')->unique();     // ví dụ: staff.orders.index
            $t->string('module_name');              // ví dụ: orders
            $t->string('action');                   // view|create|update|delete
            $t->string('area')->default('staff');   // admin|staff (để build menu theo khu)
            $t->boolean('is_active')->default(true);
            $t->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('route_permissions');
    }
};
