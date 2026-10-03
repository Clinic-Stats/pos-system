<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // weight = بە کیلۆ/تەن دەفرۆشرێت | carton = بە کارتۆن (دانە) دەفرۆشرێت
            $table->string('sell_type', 10)->default('weight')->after('kg_per_carton');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('sell_type');
        });
    }
};