<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_orders', function (Blueprint $t) {
            $t->id();
            $t->string('order_no')->unique();
            $t->unsignedBigInteger('customer_id')->nullable()->index(); // کڕیاری تۆمارکراو (ئەگەر ژمارەکە دۆزرایەوە)
            $t->boolean('claims_regular')->default(false);              // کڕیار خۆی وتی هەمیشەییم
            $t->string('name');
            $t->string('phone', 50);
            $t->string('address')->nullable();
            $t->text('note')->nullable();
            $t->string('status', 20)->default('pending')->index();      // pending / accepted / rejected
            $t->unsignedBigInteger('sale_id')->nullable();              // وەسڵی فرۆشتن دوای قبوڵکردن
            $t->unsignedBigInteger('handled_by')->nullable();
            $t->timestamp('handled_at')->nullable();
            $t->timestamps();
        });

        Schema::create('customer_order_items', function (Blueprint $t) {
            $t->id();
            $t->foreignId('customer_order_id')->constrained('customer_orders')->cascadeOnDelete();
            $t->unsignedBigInteger('product_id');
            $t->unsignedBigInteger('unit_id')->nullable();
            $t->decimal('quantity', 12, 3);
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_order_items');
        Schema::dropIfExists('customer_orders');
    }
};