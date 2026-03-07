<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();

            // ======================
            // 💰 الضريبة
            // ======================
            $table->decimal('tax_percent', 5, 2)->default(0);
            $table->boolean('tax_enabled')->default(true);

            // ======================
            // 👑 Premium
            // ======================
            $table->boolean('premium_exempt_tax')->default(true);
            $table->integer('premium_discount_percent')->default(0);

            // ======================
            // 🛑 حالة الموقع
            // ======================
            $table->boolean('site_enabled')->default(true);
            $table->text('maintenance_message')->nullable();

            // ======================
            // 💳 حدود الطلب
            // ======================
            $table->decimal('min_order_amount', 12, 2)->default(1);
            $table->decimal('max_order_amount', 12, 2)->default(100000);

            // ======================
            // 🚨 حد الإنفاق اليومي
            // ======================
            $table->decimal('daily_spend_limit', 12, 2)->default(30000);
            $table->boolean('auto_ban_on_limit')->default(true);

            // ======================
            // 💎 مكافأة التسجيل
            // ======================
            $table->decimal('register_bonus', 12, 2)->default(0);

            // ======================
            // 🔒 تفعيل العمليات
            // ======================
            $table->boolean('topup_enabled')->default(true);
            $table->boolean('voucher_enabled')->default(true);

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('settings');
    }
};