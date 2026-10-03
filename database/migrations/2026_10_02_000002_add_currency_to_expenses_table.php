<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('expenses', 'currency')) {
            Schema::table('expenses', function (Blueprint $table) {
                $table->string('currency', 3)->default('IQD')->after('amount');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('expenses', 'currency')) {
            Schema::table('expenses', function (Blueprint $table) {
                $table->dropColumn('currency');
            });
        }
    }
};