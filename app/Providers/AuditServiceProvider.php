<?php

namespace App\Providers;

use App\Models\CashHandover;
use App\Models\CustomerPayment;
use App\Models\Expense;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\SaleReturn;
use App\Models\StockLoss;
use App\Models\SupplierPayment;
use App\Observers\ActivityObserver;
use Illuminate\Support\ServiceProvider;

class AuditServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        foreach ([Sale::class, Purchase::class, SaleReturn::class, CustomerPayment::class, CashHandover::class,
                  Expense::class, SupplierPayment::class, StockLoss::class] as $model) {
            $model::observe(ActivityObserver::class);
        }
    }
}