<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('activity_logs')) {
            return;
        }

        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('user_name')->nullable();          // ناوی کارمەند لەو کاتەدا
            $table->string('action', 12)->index();            // created | updated | deleted
            $table->string('type', 30)->index();              // sale, purchase, customer_payment ...
            $table->unsignedBigInteger('model_id')->nullable();
            $table->string('label');                          // نموونە: وەسڵی فرۆشتن 1254
            $table->string('party')->nullable();              // کڕیار / دابینکەر / مەندووب
            $table->decimal('amount', 15, 2)->nullable();
            $table->string('currency', 3)->nullable();
            $table->json('details')->nullable();              // گۆڕانکارییەکان و کاڵاکانی پێش/دوای
            $table->string('ip', 45)->nullable();
            $table->timestamp('created_at')->useCurrent()->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};