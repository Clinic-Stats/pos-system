<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            // کاڵا و یەکە دەگوازرێنەوە بۆ purchase_details بۆ ئەوەی چەندین کاڵا لەخۆبگرێت
            if (Schema::hasColumn('purchases', 'product_id')) {
                $table->dropForeign(['product_id']);
                $table->dropColumn('product_id');
            }
            if (Schema::hasColumn('purchases', 'unit_id')) {
                $table->dropForeign(['unit_id']);
                $table->dropColumn('unit_id');
            }
            if (Schema::hasColumn('purchases', 'quantity')) {
                $table->dropColumn('quantity');
            }
            if (Schema::hasColumn('purchases', 'unit_buy_price')) {
                $table->dropColumn('unit_buy_price');
            }
            if (!Schema::hasColumn('purchases', 'purchase_date')) {
                $table->date('purchase_date')->nullable()->after('purchase_no');
            }
        });
    }

    public function down(): void
    {
        // down
    }
};