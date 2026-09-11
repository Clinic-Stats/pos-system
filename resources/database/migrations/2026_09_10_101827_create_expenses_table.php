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
    Schema::create('expenses', function (Blueprint $table) {
        $table->id();
        $table->string('title'); // ناونیشانی خەرجی (وەک: کرێ، مووچە، بەنزین، نانخواردن)
        $table->decimal('amount', 15, 2); // بڕی پارە
        $table->date('date'); // بەروار
        $table->string('category')->nullable(); // پۆلی خەرجی
        $table->text('note')->nullable(); // تێبینی
        $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); // کێ خەرجی کردووە
        $table->timestamps();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
