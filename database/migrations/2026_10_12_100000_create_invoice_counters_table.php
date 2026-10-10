<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * ژمارەدەری وەسڵ: یەک ڕیز بۆ هەر جۆرە وەسڵێک (ئێستا: sales).
     * ژمارە لە ناو transaction ی فرۆشتنەکەدا و بە lockForUpdate وەردەگیرێت، بۆیە
     *  - دوو کەس لە یەک کاتدا هەرگیز یەک ژمارە وەرناگرن
     *  - ئەگەر فرۆشتنەکە شکست بهێنێت، ژمارەکە نادزرێت (بەتاڵ نامێنێتەوە)
     */
    public function up(): void
    {
        Schema::create('invoice_counters', function (Blueprint $t) {
            $t->string('name')->primary();
            $t->unsignedBigInteger('last_number')->default(0);
            $t->timestamps();
        });

        // ئەگەر پێشتر وەسڵی ژمارەیی هەبێت (نموونە 1, 2, 3)، ژمارەدەر لە زۆرترینیانەوە دەست پێدەکات
        $max = 0;
        foreach (DB::table('sales')->pluck('invoice_no') as $no) {
            if (ctype_digit((string) $no)) $max = max($max, (int) $no);
        }

        DB::table('invoice_counters')->insert([
            'name'        => 'sales',
            'last_number' => $max,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_counters');
    }
};