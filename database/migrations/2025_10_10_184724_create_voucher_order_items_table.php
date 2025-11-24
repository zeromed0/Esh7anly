<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Order;
use App\Models\VoucherCode;

return new class extends Migration {
    public function up(): void {
        Schema::create('voucher_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Order::class)->constrained()->onDelete('cascade');
            $table->foreignIdFor(VoucherCode::class)->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('voucher_order_items');
    }
};