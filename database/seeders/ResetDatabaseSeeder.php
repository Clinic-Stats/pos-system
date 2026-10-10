<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ResetDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ناچالاککردنی پشکنینی پەیوەندییەکان
        Schema::disableForeignKeyConstraints();

        $tables = [
            'sale_details',
            'sales',
            'purchase_details',
            'purchases',
            'customer_order_items',
            'customer_orders',
            'returns',
            'payments',
            'expenses',
            'losses',
            'products',
            'categories',
            'customers',
            'suppliers',
            'partners',
            'activity_logs',
            'invoice_counters',
            'settings',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                DB::table($table)->truncate();
                $this->command->info("✓ خشتەی {$table} سڕایەوە");
            }
        }

        // چالاککردنەوەی پشکنینی پەیوەندییەکان
        Schema::enableForeignKeyConstraints();

        $this->command->info('✅ داتابەیسەکە بە سەرکەوتوویی سفرکرایەوە!');
    }
}