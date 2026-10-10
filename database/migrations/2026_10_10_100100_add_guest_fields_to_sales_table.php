<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // ناو / مۆبایل / ناونیشانی کڕیاری ئاسایی (ئەوانەی لە خشتەی کڕیاران نین) لەسەر وەسڵەکە
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            if (!Schema::hasColumn('sales', 'guest_name'))    $table->string('guest_name')->nullable();
            if (!Schema::hasColumn('sales', 'guest_phone'))   $table->string('guest_phone', 50)->nullable();
            if (!Schema::hasColumn('sales', 'guest_address')) $table->string('guest_address')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn(['guest_name', 'guest_phone', 'guest_address']);
        });
    }
};