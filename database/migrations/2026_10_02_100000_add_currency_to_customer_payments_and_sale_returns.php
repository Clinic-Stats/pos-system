<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // دراوی وەرگرتنەوەی قەرز (کۆنەکان دەبنە دینار)
        if (Schema::hasTable('customer_payments') && !Schema::hasColumn('customer_payments', 'currency')) {
            Schema::table('customer_payments', function (Blueprint $table) {
                $table->string('currency', 3)->default('IQD')->after('amount');
            });
        }

        // دراوی وەسڵی گەڕانەوە (کۆنەکان دەبنە دینار)
        if (Schema::hasTable('sale_returns') && !Schema::hasColumn('sale_returns', 'currency')) {
            Schema::table('sale_returns', function (Blueprint $table) {
                $table->string('currency', 3)->default('IQD')->after('total_amount');
            });
        }

        // دڵنیابوون لەوەی وەسڵی فرۆشتن ستوونی دراوی هەیە
        if (Schema::hasTable('sales') && !Schema::hasColumn('sales', 'currency')) {
            Schema::table('sales', function (Blueprint $table) {
                $table->string('currency', 3)->default('IQD');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('customer_payments', 'currency')) {
            Schema::table('customer_payments', function (Blueprint $table) {
                $table->dropColumn('currency');
            });
        }
        if (Schema::hasColumn('sale_returns', 'currency')) {
            Schema::table('sale_returns', function (Blueprint $table) {
                $table->dropColumn('currency');
            });
        }
    }
};