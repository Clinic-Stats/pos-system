<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'address',
    ];

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function payments()
    {
        return $this->hasMany(CustomerPayment::class);
    }

    public function returns()
    {
        return $this->hasMany(SaleReturn::class);
    }

    /**
     * هەر دراوێک: USD یان IQD (بەتاڵ = دینار)
     */
    public static function normalizeCurrency($currency): string
    {
        return strtoupper((string) $currency) === 'USD' ? 'USD' : 'IQD';
    }

    /**
     * حیسابی کڕیار بە جیا بۆ هەر دراوێک (بێ گۆڕینی نرخ)
     * @return array ['USD' => [purchases, paid, debt], 'IQD' => [...]]
     */
    public function currencySummary(): array
    {
        $s = [
            'USD' => ['purchases' => 0.0, 'paid' => 0.0, 'debt' => 0.0],
            'IQD' => ['purchases' => 0.0, 'paid' => 0.0, 'debt' => 0.0],
        ];

        foreach ($this->sales as $sale) {
            $c = self::normalizeCurrency($sale->currency);
            $s[$c]['purchases'] += (float) $sale->total_amount;
            $s[$c]['paid']      += (float) $sale->paid_amount;
        }

        foreach ($this->payments as $pay) {
            $c = self::normalizeCurrency($pay->currency);
            $s[$c]['paid'] += (float) $pay->amount;
        }

        foreach ($this->returns as $ret) {
            if ($ret->refund_type === 'deduct_debt') {
                $c = self::normalizeCurrency($ret->currency);
                $s[$c]['paid'] += (float) $ret->total_amount;
            }
        }

        foreach ($s as $c => $v) {
            $s[$c]['debt'] = $v['purchases'] - $v['paid'];
        }

        return $s;
    }

    // کۆنەکە - بۆ ئەو شوێنانەی (وەک ڕاپۆرت) هێشتا بەکاری دەهێنن
    public function totalDebt()
    {
        $totalSalesDebt = $this->sales()->sum('remaining_amount');
        $totalPaidLater = $this->payments()->sum('amount');

        return max(0, $totalSalesDebt - $totalPaidLater);
    }
}