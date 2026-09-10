<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Customer;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BackupAndExportController extends Controller
{
    /**
     * ١. هەناردەکردنی کاڵاکانی مەخزەن بۆ ئیکسڵ
     */
    public function exportProducts()
    {
        $fileName = 'products_stock_' . date('Y-m-d_H-i') . '.csv';

        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $products = Product::with('category')->get();

        $columns = ['کۆد', 'ناوی کاڵا', 'کاتیگۆری', 'نرخی کڕین', 'نرخی فرۆشتن', 'مەخزەن (کگ)', 'دۆخ'];

        $callback = function () use ($products, $columns) {
            $file = fopen('php://output', 'w');
            // زیادکردنی BOM بۆ ئەوەی زمانی کوردی لەناو ئیکسڵ بە دروستی بخوێنرێتەوە
            fputs($file, "\xEF\xBB\xBF");
            fputcsv($file, $columns);

            foreach ($products as $p) {
                fputcsv($file, [
                    $p->code,
                    $p->name,
                    $p->category->name ?? '-',
                    $p->base_buy_price,
                    $p->base_sale_price,
                    $p->stock_kg ?? $p->stock ?? 0,
                    $p->is_active ? 'چالاک' : 'ناچالاک',
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * ٢. هەناردەکردنی لیستی کڕیاران و قەرزەکانیان بۆ ئیکسڵ
     */
    public function exportCustomers()
    {
        $fileName = 'customers_debt_' . date('Y-m-d_H-i') . '.csv';

        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $customers = Customer::with(['sales', 'payments', 'returns'])->get();

        $columns = ['ناوی کڕیار', 'ژمارە تەلەفۆن', 'ناونیشان', 'کۆی کڕین', 'کۆی پارەدان', 'قەرزی ماوە (د.ع)'];

        $callback = function () use ($customers, $columns) {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF");
            fputcsv($file, $columns);

            foreach ($customers as $c) {
                $totalBuy = $c->sales->sum('total_amount');
                $totalPaid = $c->sales->sum('paid_amount') + $c->payments->sum('amount') + $c->returns->where('refund_type', 'deduct_debt')->sum('total_amount');
                $remainingDebt = max(0, $totalBuy - $totalPaid);

                fputcsv($file, [
                    $c->name,
                    $c->phone ?? '-',
                    $c->address ?? '-',
                    $totalBuy,
                    $totalPaid,
                    $remainingDebt,
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * ٣. باکئەپی داتابەیس (Download .SQL Database Backup)
     */
    public function backupDatabase()
    {
        $dbName = config('database.connections.mysql.database');
        $dbHost = config('database.connections.mysql.host');
        $dbUser = config('database.connections.mysql.username');
        $dbPass = config('database.connections.mysql.password');

        $fileName = 'backup_' . $dbName . '_' . date('Y-m-d_H-i-s') . '.sql';

        $headers = [
            "Content-type"        => "application/sql",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function () use ($dbName) {
            $out = fopen('php://output', 'w');

            // هێدەری فایلی SQL
            fwrite($out, "-- SQL Database Backup\n");
            fwrite($out, "-- Date: " . date('Y-m-d H:i:s') . "\n");
            fwrite($out, "-- Database: {$dbName}\n\n");
            fwrite($out, "SET FOREIGN_KEY_CHECKS=0;\n\n");

            // دەرهێنانی خشتەکان
            $tables = DB::select('SHOW TABLES');
            $key = "Tables_in_{$dbName}";

            foreach ($tables as $t) {
                $table = $t->$key;

                // دروستکردنی خشتە
                $createTable = DB::select("SHOW CREATE TABLE `{$table}`");
                fwrite($out, "\n\n-- Structure for table `{$table}`\n");
                fwrite($out, "DROP TABLE IF EXISTS `{$table}`;\n");
                fwrite($out, $createTable[0]->{'Create Table'} . ";\n\n");

                // دەرهێنانی داتاکان
                $rows = DB::table($table)->get();
                if ($rows->count() > 0) {
                    fwrite($out, "-- Data for table `{$table}`\n");
                    foreach ($rows as $row) {
                        $rowArray = (array) $row;
                        $keys = array_map(fn($k) => "`$k`", array_keys($rowArray));
                        $values = array_map(function ($val) {
                            if (is_null($val)) return "NULL";
                            return "'" . addslashes($val) . "'";
                        }, array_values($rowArray));

                        fwrite($out, "INSERT INTO `{$table}` (" . implode(', ', $keys) . ") VALUES (" . implode(', ', $values) . ");\n");
                    }
                }
            }

            fwrite($out, "\nSET FOREIGN_KEY_CHECKS=1;\n");
            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }
}