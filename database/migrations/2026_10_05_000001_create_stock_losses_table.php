<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('stock_losses')) {
            return;
        }

        Schema::create('stock_losses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained();
            $table->decimal('quantity', 15, 3);            // بە یەکەی کۆگا: کیلۆ، یان کارتۆن بۆ کاڵای کارتۆنی
            $table->decimal('unit_cost_usd', 15, 4);       // نرخی کڕین (دۆلار) لەو کاتەدا
            $table->decimal('total_cost_usd', 15, 4);      // زیانی دارایی بە دۆلار
            $table->string('reason', 20);                  // expired | damaged | lost | other
            $table->string('note')->nullable();
            $table->string('source', 20)->default('manual'); // manual | sale_return
            $table->unsignedBigInteger('sale_return_id')->nullable()->index();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->dateTime('loss_date');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_losses');
    }
};