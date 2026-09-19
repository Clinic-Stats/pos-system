<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('purchases', function (Blueprint $table) {
            if (!Schema::hasColumn('purchases', 'paid_amount')) {
                $table->decimal('paid_amount', 15, 2)->default(0)->after('total_amount');
            }
            if (!Schema::hasColumn('purchases', 'remaining_amount')) {
                $table->decimal('remaining_amount', 15, 2)->default(0)->after('paid_amount');
            }
            if (!Schema::hasColumn('purchases', 'payment_type')) {
                $table->string('payment_type')->default('cash')->after('remaining_amount');
            }
        });
    }

    public function down()
    {
        Schema::table('purchases', function (Blueprint $table) {
            $table->dropColumn(['paid_amount', 'remaining_amount', 'payment_type']);
        });
    }
};