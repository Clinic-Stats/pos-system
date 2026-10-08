{{-- resources/views/partials/sale-debt.blade.php --}}
{{-- قەرزی کڕیار پێش و دوای ئەم وەسڵە. پێویستی بە $sale و $isUsd هەیە --}}
@php
$debtBefore = 0; $debtAfter = 0; $showDebt = false;

if ($sale->customer_id) {
    // گۆڕینی بڕەکە بۆ دراوی ئەم وەسڵە (ئەگەر وەسڵە کۆنەکان بە دراوێکی تر بوون)
    $toCur = function ($s, $amount) use ($isUsd) {
        $amount = (float) $amount;
        $sUsd = ($s->currency ?? 'IQD') === 'USD';
        $r = (float) ($s->exchange_rate ?: 1500);
        if ($sUsd === $isUsd) return $amount;
        return $isUsd ? $amount / $r : $amount * $r;
    };

    // قەرزی وەسڵە پێشووەکانی هەمان کڕیار (ئەوانەی پێش ئەم وەسڵە تۆمارکراون)
    $before = \App\Models\Sale::where('customer_id', $sale->customer_id)
        ->where('payment_type', 'debt')
        ->where('id', '!=', $sale->id)
        ->get()
        ->filter(fn($s) => $s->created_at->lt($sale->created_at)
            || ($s->created_at->eq($sale->created_at) && $s->id < $sale->id));

    $debtBefore = round($before->sum(fn($s) => $toCur($s, $s->remaining_amount)), 2);
    $thisRemain = $sale->payment_type === 'debt' ? $toCur($sale, $sale->remaining_amount) : 0;
    $debtAfter  = round($debtBefore + $thisRemain, 2);

    // تەنها ئەگەر وەسڵەکە قەرزە، یان کڕیارەکە قەرزی پێشووی هەیە
    $showDebt = $sale->payment_type === 'debt' || $debtBefore > 0;
}
@endphp