<?php

namespace App\Observers;

use App\Models\ActivityLog;
use App\Models\Customer;
use App\Models\CustomerPayment;
use App\Models\CashHandover;
use App\Models\Expense;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\SaleReturn;
use App\Models\StockLoss;
use App\Models\Supplier;
use App\Models\SupplierPayment;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * هەموو زیادکردن / دەستکاری / سڕینەوەی وەسڵ و پارەکان لێرەوە تۆمار دەکرێن.
 * تۆمارەکە دوای سەرکەوتنی گۆڕانکارییەکە (commit) دەنووسرێت، ئەگەر هەڵە ڕووبدات و گەڕانەوە (rollback) هەبێت تۆمار نابێت.
 */
class ActivityObserver
{
    private const SKIP = ['updated_at'];

    private function type(Model $m): ?string
    {
        return match (true) {
            $m instanceof Sale            => 'sale',
            $m instanceof Purchase        => 'purchase',
            $m instanceof SaleReturn      => 'return',
            $m instanceof CustomerPayment => 'customer_payment',
            $m instanceof CashHandover    => 'handover',
            $m instanceof Expense         => 'expense',
            $m instanceof SupplierPayment => 'supplier_payment',
            $m instanceof StockLoss       => 'loss',
            default                       => null,
        };
    }

    /** [ناونیشان، لایەنی بەرامبەر، بڕ، دراو] */
    private function describe(Model $m, string $type): array
    {
        $name = ActivityLog::TYPES[$type];
        try {
            return match ($type) {
                'sale'             => [$name . ' ' . $m->invoice_no, $m->customer->name ?? 'کڕیاری گشتی', $m->total_amount, $m->currency],
                'purchase'         => [$name . ' ' . ($m->purchase_no ?? $m->invoice_no), $m->supplier->name ?? null, $m->total_amount, $m->currency],
                'return'           => [$name . ' ' . $m->return_no, $m->customer->name ?? 'کڕیاری گشتی', $m->total_amount, $m->currency],
                'customer_payment' => [$name, $m->customer->name ?? null, $m->amount, $m->currency],
                'handover'         => [$name . ' ' . $m->receipt_no, $m->mandub->name ?? null, $m->amount, $m->currency],
                'expense'          => [$name . ': ' . $m->title, $m->user->name ?? null, $m->amount, $m->currency ?? 'IQD'],
                'supplier_payment' => [$name, $m->supplier->name ?? null, $m->amount, $m->currency],
                'loss'             => [$name . ': ' . ($m->product->name ?? '—'), null, $m->total_cost_usd, 'USD'],
            };
        } catch (\Throwable $e) {
            return [$name, null, null, null];
        }
    }

    /** ژمارەی کڕیار / دابینکەر / کارمەند دەگۆڕێت بۆ ناو */
    private function resolve(string $key, $value)
    {
        if ($value === null || $value === '') {
            return $value;
        }
        $map = ['customer_id' => Customer::class, 'supplier_id' => Supplier::class,
                'user_id' => User::class, 'mandub_id' => User::class, 'received_by' => User::class];

        if (isset($map[$key])) {
            return $map[$key]::find($value)?->name ?? ('#' . $value);
        }
        return $value;
    }

    private function write(Model $m, string $action, array $extra = []): void
    {
        $type = $this->type($m);
        if (!$type) {
            return;
        }

        [$label, $party, $amount, $currency] = $this->describe($m, $type);
        $user = auth()->user();
        $base = [
            'user_id'   => $user?->id,
            'user_name' => $user?->name,
            'action'    => $action,
            'type'      => $type,
            'model_id'  => $m->getKey(),
            'label'     => $label,
            'party'     => $party,
            'amount'    => $amount,
            'currency'  => $currency ? strtoupper($currency) : null,
            'ip'        => request()->ip(),
            'created_at' => now(),
        ];

        DB::afterCommit(function () use ($base, $extra, $m, $action) {
            try {
                $details = $extra;
                if ($action !== 'deleted') {
                    $details['items_after'] = ActivityLog::items($m, true);
                }
                if ($action === 'deleted') {
                    $details['items_before'] = ActivityLog::stashed($m);
                }
                if ($action === 'updated') {
                    $details['items_before'] = ActivityLog::stashed($m);
                }
                ActivityLog::create($base + ['details' => array_filter($details, fn($v) => $v !== null && $v !== [])]);
            } catch (\Throwable $e) {
                report($e);   // تۆمارکردن نابێت کارەکە بوەستێنێت
            }
        });
    }

    public function created(Model $m): void
    {
        $this->write($m, 'created');
    }

    public function updated(Model $m): void
    {
        $changes = array_diff_key($m->getChanges(), array_flip(self::SKIP));
        $old = []; $new = [];
        foreach ($changes as $key => $value) {
            $old[$key] = $this->resolve($key, $m->getOriginal($key));
            $new[$key] = $this->resolve($key, $value);
        }
        $this->write($m, 'updated', ['old' => $old, 'new' => $new]);
    }

    public function deleted(Model $m): void
    {
        $snap = array_diff_key($m->getAttributes(), array_flip(self::SKIP));
        foreach ($snap as $k => $v) { $snap[$k] = $this->resolve($k, $v); }
        $this->write($m, 'deleted', ['old' => $snap]);
    }
}