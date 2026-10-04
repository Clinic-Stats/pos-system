<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id', 'user_name', 'action', 'type', 'model_id', 'label', 'party',
        'amount', 'currency', 'details', 'ip', 'created_at',
    ];

    protected $casts = ['details' => 'array', 'created_at' => 'datetime'];

    public const ACTIONS = ['created' => 'تۆمارکردن', 'updated' => 'دەستکاری', 'deleted' => 'سڕینەوە'];

    public const TYPES = [
        'sale'             => 'وەسڵی فرۆشتن',
        'purchase'         => 'وەسڵی کڕین',
        'return'           => 'گەڕاوەی فرۆشتن',
        'customer_payment' => 'وەرگرتنی پارە لە کڕیار',
        'handover'         => 'تەسلیماتی مەندووب',
        'expense'          => 'خەرجی',
        'supplier_payment' => 'پارەدان بە دابینکەر',
        'loss'             => 'زیانی کاڵا',
    ];

    public const FIELDS = [
        'invoice_no' => 'ژمارەی وەسڵ', 'purchase_no' => 'ژمارەی وەسڵ', 'return_no' => 'ژمارەی وەسڵ', 'receipt_no' => 'ژمارەی پسوولە',
        'customer_id' => 'کڕیار', 'supplier_id' => 'دابینکەر', 'user_id' => 'کارمەند', 'mandub_id' => 'مەندووب', 'received_by' => 'وەرگر',
        'total_amount' => 'کۆی وەسڵ', 'total_cost' => 'کۆی تێچوو', 'total_profit' => 'قازانج', 'paid_amount' => 'پارەی دراو',
        'remaining_amount' => 'ماوە (قەرز)', 'payment_type' => 'جۆری پارەدان', 'currency' => 'دراو', 'exchange_rate' => 'نرخی ئاڵوگۆڕ',
        'discount' => 'داشکاندن', 'amount' => 'بڕی پارە', 'payment_date' => 'بەرواری پارەدان', 'handover_date' => 'بەرواری تەسلیمات',
        'created_at' => 'بەروار', 'purchase_date' => 'بەروار', 'loss_date' => 'بەروار', 'date' => 'بەروار', 'note' => 'تێبینی', 'notes' => 'تێبینی',
        'title' => 'ناونیشان', 'category' => 'پۆل', 'refund_type' => 'شێوازی گەڕانەوە', 'reason' => 'هۆکار', 'quantity' => 'بڕ',
        'total_cost_usd' => 'زیان ($)',
    ];

    /** کاڵاکانی پێش دەستکاری / سڕینەوە (لە کۆنترۆڵەرەکانەوە پێش گۆڕین دەپارێزرێن) */
    public static array $stash = [];

    public static function stash(Model $model): void
    {
        if (!method_exists($model, 'details')) {
            return;
        }
        self::$stash[get_class($model) . ':' . $model->getKey()] = self::items($model);
    }

    public static function stashed(Model $model): ?array
    {
        return self::$stash[get_class($model) . ':' . $model->getKey()] ?? null;
    }

    /** لیستی کاڵاکانی وەسڵ بە شێوەیەکی سادە */
    public static function items(Model $model, bool $fresh = false): ?array
    {
        if (!method_exists($model, 'details')) {
            return null;
        }

        if ($fresh) {
            $model->unsetRelation('details');
        }
        $model->loadMissing(['details.product', 'details.unit']);

        return $model->details->map(fn($d) => [
            'product' => $d->product->name ?? '—',
            'unit'    => $d->unit->name ?? '',
            'qty'     => (float) $d->quantity,
            'price'   => (float) ($d->unit_price ?? $d->unit_buy_price ?? 0),
            'total'   => (float) ($d->line_total ?? $d->subtotal ?? 0),
            'cond'    => $d->condition_type ?? null,
        ])->values()->all();
    }
}