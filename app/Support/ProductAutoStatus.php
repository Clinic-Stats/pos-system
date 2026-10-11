<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * چالاک / ناچالاک کردنی کاڵا بە خۆکار بەپێی کۆگا:
 *  - کۆگا سفر یان کەمتر  ← ناچالاک (و نیشانە دەکرێت کە خۆکارە)
 *  - کۆگا زیاتر لە سفر   ← تەنها ئەو کاڵایانە چالاک دەکرێنەوە کە خۆکار ناچالاک کرابوون
 * ئەو کاڵایانەی بە دەست ناچالاک کراون (و کۆگایان هەیە) دەستکاری ناکرێن.
 */
class ProductAutoStatus
{
    public static function sync(): void
    {
        if (!Schema::hasColumn('products', 'auto_disabled')) {
            return;   // هێشتا migration جێبەجێ نەکراوە
        }

        $col = Schema::hasColumn('products', 'stock_kg') ? 'stock_kg' : 'stock';

        // نەماو ← ناچالاک
        DB::table('products')
            ->where($col, '<=', 0)
            ->where('is_active', 1)
            ->update(['is_active' => 0, 'auto_disabled' => 1]);

        // کۆگای هاتەوە ← چالاک (تەنها ئەوانەی خۆکار ناچالاک کرابوون)
        DB::table('products')
            ->where($col, '>', 0)
            ->where('auto_disabled', 1)
            ->update(['is_active' => 1, 'auto_disabled' => 0]);
    }
}