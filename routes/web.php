<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SaleReturnController;
use App\Http\Controllers\MandubDashboardController;
use App\Http\Controllers\PartnerController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CashHandoverController;
use App\Http\Controllers\GlobalSearchController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\StockLossController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\BackupAndExportController;


// ڕووتی لۆگین و دەرچوون
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// هەموو بەشەکانی ناوەوەی سیستەم
Route::middleware(['auth'])->group(function () {

    // ============================================
    // ڕاوتێکی کاتی بۆ پاککردنەوەی کاش
    // تەنها ئەو کەسانەی لۆگینیان کردووە دەتوانن بەکاری بهێنن
    // ============================================
    Route::middleware('permission:backup')->get('/karwan-cache', function() {
        \Illuminate\Support\Facades\Artisan::call('optimize:clear');
        \Illuminate\Support\Facades\Artisan::call('route:clear');
        \Illuminate\Support\Facades\Artisan::call('view:clear');
        \Illuminate\Support\Facades\Artisan::call('config:clear');
        \Illuminate\Support\Facades\Artisan::call('cache:clear');
        return '<div style="font-family:sans-serif;text-align:center;margin-top:100px;color:green;font-size:24px">✅ کاشەکە بە سەرکەوتوویی پاککرایەوە!</div>';
    });

    // نوێکردنەوەی نرخی ئاڵوگۆڕ لە شاشەی POS
    Route::post('/update-exchange-rate', [SettingController::class, 'updateExchangeRate'])->name('settings.updateExchangeRate');

    // لیستی هەموو فرۆشتنەکان
    Route::get('/sales-list', [SaleController::class, 'listSales'])->name('sales.list')->middleware('permission:pos');
    
    // ڕادەستکردنی پارە (Handover)
    Route::post('/handovers', [CashHandoverController::class, 'store'])->name('handovers.store');
    Route::put('/handovers/{id}', [CashHandoverController::class, 'update'])->name('handovers.update');
    Route::get('/handovers/{id}/print', [CashHandoverController::class, 'printReceipt'])->name('handovers.print');

    // لاپەڕەی سەرەکی و POS
    Route::get('/', [SaleController::class, 'index'])->name('pos.index')->middleware('permission:pos');
    Route::post('/sales', [SaleController::class, 'store'])->name('sales.store')->middleware('permission:pos');
    Route::get('/sales/{id}/edit', [SaleController::class, 'edit'])->name('sales.edit')->middleware('permission:pos');
    Route::put('/sales/{id}', [SaleController::class, 'update'])->name('sales.update')->middleware('permission:pos');
    Route::delete('/sales/{id}', [SaleController::class, 'destroy'])->name('sales.destroy')->middleware('permission:pos');
    Route::get('/sales/{id}/print', [SaleController::class, 'print'])->name('sales.print')->middleware('permission:pos');
    Route::get('/sales/print/{id}', [SaleController::class, 'print'])->name('sales.print.alt')->middleware('permission:pos');

    // چالاکییەکانی مەندووب
    Route::get('/mandub-dashboard', [MandubDashboardController::class, 'index'])->name('mandub.dashboard')->middleware('permission:mandub_dashboard');

    // کڕیاران و دەستکاری و قەرزدانەوە
    Route::middleware('permission:customers')->group(function () {
        Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
        Route::post('/customers', [CustomerController::class, 'store'])->name('customers.store');
        Route::put('/customers/{id}', [CustomerController::class, 'update'])->name('customers.update');
        Route::delete('/customers/{id}', [CustomerController::class, 'destroy'])->name('customers.destroy');
        Route::post('/customers/{id}/payment', [CustomerController::class, 'addPayment'])->name('customers.payment');
        Route::get('/customers/{id}/statement', [CustomerController::class, 'statement'])->name('customers.statement');
        Route::put('/customer-payments/{id}', [CustomerController::class, 'updatePayment'])->name('customer_payments.update');
        Route::delete('/customer-payments/{id}', [CustomerController::class, 'destroyPayment'])->name('customer_payments.destroy');
    });

    // گەڕاوەکان
    Route::middleware('permission:returns')->group(function () {
        Route::resource('returns', SaleReturnController::class);
        Route::get('returns/{id}/print', [SaleReturnController::class, 'print'])->name('returns.print');
    });
  
    // کاڵاکان، کۆگا، کاتیگۆری و یەکەکان
    Route::middleware('permission:products')->group(function () {
        Route::get('/products', [ProductController::class, 'index'])->name('products.index');
        Route::post('/products', [ProductController::class, 'store'])->name('products.store');
        
        // 👇 ئەم دوو ڕاوتە بۆ نوێکردنەوەی کاڵا زۆر گرنگن
        Route::put('/products/{id}', [ProductController::class, 'update'])->name('products.update');
        Route::patch('/products/{id}', [ProductController::class, 'update'])->name('products.update.patch');
        
        Route::post('/products/{id}/add-stock', [ProductController::class, 'addStock'])->name('products.addStock');
        Route::delete('/products/{id}', [ProductController::class, 'destroy'])->name('products.destroy');
        Route::patch('/products/{id}/toggle', [ProductController::class, 'toggleStatus'])->name('products.toggle');

        Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
        Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
        Route::put('/categories/{id}', [CategoryController::class, 'update'])->name('categories.update');  
        Route::delete('/categories/{id}', [CategoryController::class, 'destroy'])->name('categories.destroy');
        

        Route::get('/global-search', [GlobalSearchController::class, 'search'])->name('global.search');

        // زیانی کاڵا (بەسەرچوو / تەلەف)
        Route::get('/stock-losses', [StockLossController::class, 'index'])->name('losses.index');
        Route::post('/stock-losses', [StockLossController::class, 'store'])->name('losses.store');
        Route::delete('/stock-losses/{id}', [StockLossController::class, 'destroy'])->name('losses.destroy');

        Route::get('/units', [UnitController::class, 'index'])->name('units.index');
        Route::post('/units', [UnitController::class, 'store'])->name('units.store');
        Route::put('/units/{id}', [UnitController::class, 'update'])->name('units.update'); 
        Route::delete('/units/{id}', [UnitController::class, 'destroy'])->name('units.destroy');
    });

    // کڕین و دابینکەران
    Route::middleware('permission:purchases')->group(function () {
        Route::get('/purchases', [PurchaseController::class, 'index'])->name('purchases.index');
        Route::get('/purchases/create', [PurchaseController::class, 'create'])->name('purchases.create');
        Route::post('/purchases', [PurchaseController::class, 'store'])->name('purchases.store');
        Route::get('/purchases/{id}/edit', [PurchaseController::class, 'edit'])->name('purchases.edit');
        Route::put('/purchases/{id}', [PurchaseController::class, 'update'])->name('purchases.update');
        Route::delete('/purchases/{id}', [PurchaseController::class, 'destroy'])->name('purchases.destroy');
        Route::get('/purchases/{id}/print', [PurchaseController::class, 'print'])->name('purchases.print');

        Route::get('/suppliers', [SupplierController::class, 'index'])->name('suppliers.index');
        Route::post('/suppliers', [SupplierController::class, 'store'])->name('suppliers.store');
        Route::put('/suppliers/{id}', [SupplierController::class, 'update'])->name('suppliers.update');
        Route::delete('/suppliers/{id}', [SupplierController::class, 'destroy'])->name('suppliers.destroy');

        // کەشفی حسابی دابینکەر و پارەدان پێیان
        Route::get('/suppliers/{id}/statement', [SupplierController::class, 'statement'])->name('suppliers.statement');
        Route::post('/suppliers/{id}/payments', [SupplierController::class, 'storePayment'])->name('suppliers.payments.store');
        Route::delete('/supplier-payments/{id}', [SupplierController::class, 'destroyPayment'])->name('suppliers.payments.destroy');
    });

    // هاوبەشەکان
    Route::middleware('permission:partners')->group(function () {
        Route::get('/partners', [PartnerController::class, 'index'])->name('partners.index');
        Route::post('/partners', [PartnerController::class, 'store'])->name('partners.store');
        Route::put('/partners/{id}', [PartnerController::class, 'update'])->name('partners.update');
        Route::delete('/partners/{id}', [PartnerController::class, 'destroy'])->name('partners.destroy');

        Route::post('/partners/{id}/transaction', [PartnerController::class, 'addTransaction'])->name('partners.transaction');
        Route::delete('/partners/transaction/{id}', [PartnerController::class, 'destroyTransaction'])->name('partners.transaction.destroy');

        Route::get('/partners/all-report', [PartnerController::class, 'allReport'])->name('partners.allReport');
        Route::get('/partners/{id}/statement', [PartnerController::class, 'show'])->name('partners.show');
    });

    // تۆماری چالاکییەکان (تەنها ئەدمین)
    Route::get('/activity-log', [ActivityLogController::class, 'index'])->name('audit.index')->middleware('permission:audit');

    // خەرجییەکان
    Route::get('/expenses', [ExpenseController::class, 'index'])->name('expenses.index');
    Route::post('/expenses', [ExpenseController::class, 'store'])->name('expenses.store');
    Route::put('/expenses/{id}', [ExpenseController::class, 'update'])->name('expenses.update');
    Route::delete('/expenses/{id}', [ExpenseController::class, 'destroy'])->name('expenses.destroy');
    Route::get('/expenses/report', [ExpenseController::class, 'report'])->name('expenses.report');

    // ڕێکخستنی وەسڵ
    Route::get('/settings/receipt', [SettingController::class, 'index'])->name('settings.receipt')->middleware('permission:settings');
    Route::put('/settings/receipt', [SettingController::class, 'update'])->name('settings.receipt.update')->middleware('permission:settings');

    // ڕاپۆرتەکان
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index')->middleware('permission:reports');

    // هەناردەکردن بۆ ئیکسڵ و باکئەپ
    Route::get('/export/products', [BackupAndExportController::class, 'exportProducts'])->name('export.products')->middleware('permission:products');
    Route::get('/export/customers', [BackupAndExportController::class, 'exportCustomers'])->name('export.customers')->middleware('permission:customers');
    Route::get('/backup/database', [BackupAndExportController::class, 'backupDatabase'])->name('backup.database')->middleware('permission:backup');

    Route::post('/products/import-csv', [App\Http\Controllers\ProductController::class, 'importCsv'])->name('products.importCsv');

    // بەڕێوەبردنی کارمەندان (Users)
    Route::resource('users', UserController::class)->middleware('permission:users');

    // API بۆ وەرگرتنی لیستی دابینکەران
    Route::get('/api/suppliers-list', function() {
        return response()->json(\App\Models\Supplier::orderBy('name')->get(['id', 'name']));
    });
});

Route::get('/karwan-clear-data', function () {
    if (!auth()->check() || !auth()->user()->canReceiveCash()) {
        abort(403, 'ڕێگەپێدراو نییە');
    }

    $keep = [
        'users', 'migrations', 'roles', 'permissions',
        'model_has_roles', 'model_has_permissions', 'role_has_permissions',
        'password_resets', 'failed_jobs', 'personal_access_tokens',
        'sessions', 'cache', 'cache_locks', 'jobs', 'job_batches',
    ];

    $dbName = \DB::getDatabaseName();
    $key = 'Tables_in_' . $dbName;
    $tables = \DB::select('SHOW TABLES');

    \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
    $cleared = [];
    foreach ($tables as $t) {
        $name = $t->$key;
        if (in_array($name, $keep)) continue;
        \DB::table($name)->truncate();
        $cleared[] = $name;
    }
    \DB::statement('SET FOREIGN_KEY_CHECKS=1;');

    return '<div style="font-family:sans-serif;padding:30px;background:#0b1220;color:#10b981;min-height:100vh">'
        . '<h2>✅ پاککرایەوە</h2><ul><li>' . implode('</li><li>', $cleared) . '</li></ul>'
        . '<p>خشتەکانی پارێزراو: users + ڕۆڵەکان</p></div>';
});