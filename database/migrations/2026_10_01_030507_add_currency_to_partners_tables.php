<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('partners', function (Blueprint $table) {
            $table->string('currency', 10)->default('USD')->after('capital_amount');
        });

        Schema::table('partner_transactions', function (Blueprint $table) {
            $table->string('currency', 10)->default('USD')->after('amount');
            $table->decimal('exchange_rate', 15, 2)->default(1500)->after('currency');
        });
    }

    public function down(): void
    {
        Schema::table('partners', function (Blueprint $table) {
            $table->dropColumn('currency');
        });
        Schema::table('partner_transactions', function (Blueprint $table) {
            $table->dropColumn(['currency', 'exchange_rate']);
        });
    }
};