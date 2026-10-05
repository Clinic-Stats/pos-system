<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchase_details', function (Blueprint $table) {
            // وێنەی تێچووی تێکڕا لە کاتی هەر کڕینێک، بۆ گەڕاندنەوەی ڕێک و دروست لە سڕینەوە / دەستکاری
            if (!Schema::hasColumn('purchase_details', 'stock_before')) $table->decimal('stock_before', 18, 3)->nullable();
            if (!Schema::hasColumn('purchase_details', 'cost_before'))  $table->decimal('cost_before', 18, 6)->nullable();
            if (!Schema::hasColumn('purchase_details', 'cost_after'))   $table->decimal('cost_after', 18, 6)->nullable();
            if (!Schema::hasColumn('purchase_details', 'added_units'))  $table->decimal('added_units', 18, 3)->nullable();
            if (!Schema::hasColumn('purchase_details', 'price_usd_unit')) $table->decimal('price_usd_unit', 18, 6)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('purchase_details', function (Blueprint $table) {
            foreach (['stock_before', 'cost_before', 'cost_after', 'added_units', 'price_usd_unit'] as $c) {
                if (Schema::hasColumn('purchase_details', $c)) $table->dropColumn($c);
            }
        });
    }
};