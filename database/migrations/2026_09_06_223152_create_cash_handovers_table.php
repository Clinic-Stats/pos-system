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
      Schema::create('cash_handovers', function (Blueprint $table) {
    $table->id();
    $table->string('receipt_no')->unique(); // ژمارەی وەسڵ
    $table->foreignId('mandub_id')->constrained('users')->cascadeOnDelete(); // مەندووبی تەسلیمکار
    $table->foreignId('received_by')->constrained('users')->cascadeOnDelete(); // وەرگر (ئەدمین/سندوق)
    $table->decimal('amount', 15, 2); // بڕی پارەی دراو
    $table->string('note')->nullable(); // تێبینی
    $table->timestamp('handover_date')->useCurrent(); // بەروار و کات
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_handovers');
    }
};
