<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ئەگەر ستوونەکە پێشتر هەبوو، هیچ ناکات
        if (!Schema::hasColumn('sales', 'discount')) {
            Schema::table('sales', function (Blueprint $table) {
                $table->decimal('discount', 15, 2)->default(0);
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('sales', 'discount')) {
            Schema::table('sales', function (Blueprint $table) {
                $table->dropColumn('discount');
            });
        }
    }
};
