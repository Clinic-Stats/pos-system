<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // تێبینی دابینکەر (فۆڕمەکە هەیبوو بەڵام نەدەپارێزرا)
        if (!Schema::hasColumn('suppliers', 'note')) {
            Schema::table('suppliers', function (Blueprint $table) {
                $table->text('note')->nullable();
            });
        }

        // پارەدان بە دابینکەر دوای کڕین (بۆ کەمکردنەوەی قەرز)
        if (!Schema::hasTable('supplier_payments')) {
            Schema::create('supplier_payments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('supplier_id')->constrained()->cascadeOnDelete();
                $table->decimal('amount', 15, 2);
                $table->string('currency', 3)->default('USD');
                $table->date('payment_date');
                $table->string('note')->nullable();
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('supplier_payments');
        if (Schema::hasColumn('suppliers', 'note')) {
            Schema::table('suppliers', function (Blueprint $table) {
                $table->dropColumn('note');
            });
        }
    }
};