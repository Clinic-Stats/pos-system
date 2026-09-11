<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up(): void
{
    Schema::table('sales', function (Blueprint $table) {
        $table->decimal('paid_amount', 15, 2)->default(0)->after('total_amount');
        $table->decimal('remaining_amount', 15, 2)->default(0)->after('paid_amount');
        $table->enum('payment_type', ['cash', 'debt'])->default('cash')->after('remaining_amount');
    });
}

public function down(): void
{
    Schema::table('sales', function (Blueprint $table) {
        $table->dropColumn(['paid_amount', 'remaining_amount', 'payment_type']);
    });
}
};
