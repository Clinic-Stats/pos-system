<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RenumberInvoices extends Command
{
    protected $signature = 'invoices:renumber {--force : بێ پرسیار}';
    protected $description = 'ژمارەی هەموو وەسڵەکانی فرۆشتن دووبارە دەکاتەوە: 1, 2, 3 ... بەپێی بەروار';

    public function handle(): int
    {
        $count = DB::table('sales')->count();
        $this->warn("ئەمە ژمارەی {$count} وەسڵی فرۆشتن دەگۆڕێت (1 تا {$count}). ژمارەی وەسڵە چاپکراوەکانی پێشوو دەگۆڕدرێت!");

        if (!$this->option('force') && !$this->confirm('دڵنیایت؟')) {
            $this->info('هیچ نەگۆڕدرا.');
            return self::SUCCESS;
        }

        DB::transaction(function () {
            $ids = DB::table('sales')->orderBy('created_at')->orderBy('id')->pluck('id');

            // قۆناغی ١: ناوی کاتی، بۆ ئەوەی هەڵەی unique نەدات
            foreach ($ids as $id) {
                DB::table('sales')->where('id', $id)->update(['invoice_no' => 'TMP-' . $id]);
            }
            // قۆناغی ٢: ژمارەی ڕاستەقینە
            $n = 0;
            foreach ($ids as $id) {
                $n++;
                DB::table('sales')->where('id', $id)->update(['invoice_no' => (string) $n]);
            }

            DB::table('invoice_counters')->updateOrInsert(
                ['name' => 'sales'],
                ['last_number' => $n, 'updated_at' => now(), 'created_at' => now()]
            );
        });

        $this->info('تەواو بوو. وەسڵی نوێ لە ژمارەی ' . (DB::table('invoice_counters')->where('name', 'sales')->value('last_number') + 1) . ' دەست پێدەکات.');
        return self::SUCCESS;
    }
}