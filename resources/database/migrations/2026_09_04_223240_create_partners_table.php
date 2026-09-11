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
    Schema::create('partners', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('phone')->nullable();
        $table->decimal('share_percentage', 5, 2)->default(0); // ڕێژەی پشک %
        $table->decimal('capital_amount', 15, 2)->default(0); // سەرمایەی بەشداربوو
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('partners');
}
};
