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
    Schema::dropIfExists('settings');

    Schema::create('settings', function (Blueprint $table) {
        $table->id();
        $table->string('shop_name')->default('فرۆشگای من');
        $table->string('shop_phone')->nullable();
        $table->string('shop_address')->nullable();
        $table->string('shop_logo')->nullable();
        $table->text('invoice_footer')->nullable();
        $table->string('receipt_width')->default('80mm');
        $table->boolean('show_barcode')->default(true);
        $table->timestamps();
    });

    // بەکارهێنانی DB بۆ ئەوەی هیچ کات تووشی هەڵەی مۆدێل نەبێتەوە
    \Illuminate\Support\Facades\DB::table('settings')->insert([
        'shop_name'      => 'مارکێتی نموونەیی',
        'shop_phone'     => '0750 000 0000',
        'shop_address'   => 'هەڵەبجە - شەقامی سەرەکی',
        'invoice_footer' => 'سوپاس بۆ سەردانەکەتان، کاڵای فرۆشراو دەگۆڕدرێتەوە تا ٢٤ کاتژمێر.',
        'receipt_width'  => '80mm',
        'show_barcode'   => true,
        'created_at'     => now(),
        'updated_at'     => now(),
    ]);
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
