<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // شوێنی کڕیار (GPS) لەگەڵ داواکاری
    public function up(): void
    {
        Schema::table('customer_orders', function (Blueprint $t) {
            if (!Schema::hasColumn('customer_orders', 'latitude'))  $t->decimal('latitude', 10, 7)->nullable();
            if (!Schema::hasColumn('customer_orders', 'longitude')) $t->decimal('longitude', 10, 7)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('customer_orders', function (Blueprint $t) {
            $t->dropColumn(['latitude', 'longitude']);
        });
    }
};