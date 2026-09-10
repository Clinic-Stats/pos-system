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
    Schema::create('partner_transactions', function (Blueprint $table) {
        $table->id();
        $table->foreignId('partner_id')->constrained()->cascadeOnDelete();
        $table->enum('type', ['deposit', 'withdraw']); // زیادکردنی سەرمایە یان کشانەوەی قازانج
        $table->decimal('amount', 15, 2);
        $table->text('note')->nullable();
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('partner_transactions');
}
};
