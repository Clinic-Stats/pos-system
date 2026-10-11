<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // نیشانەی ئەوەی کاڵایەک بە خۆکار (لە بەر نەمانی کۆگا) ناچالاک کراوە
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'auto_disabled')) {
                $table->boolean('auto_disabled')->default(false);
            }
        });

        // ئەو کاڵایانەی ئێستا ناچالاکن و کۆگایان سفرە (بە دەست ناچالاک کراون لە بەر نەمان)
        // دەکرێن بە «خۆکار»، بۆ ئەوەی کاتێک کۆگا زیاد کرا خۆیان چالاک ببنەوە
        $col = Schema::hasColumn('products', 'stock_kg') ? 'stock_kg' : 'stock';
        DB::table('products')
            ->where('is_active', 0)
            ->where($col, '<=', 0)
            ->update(['auto_disabled' => 1]);
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('auto_disabled');
        });
    }
};