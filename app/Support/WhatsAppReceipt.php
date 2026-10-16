<?php

namespace App\Support;

use App\Models\Customer;
use App\Models\Setting;

/**
 * ناردنی وەسڵ بە واتساپ (بێ API و بێ تێچوون: لینکی wa.me)
 * دوگمەکە واتساپ دەکاتەوە و نامەکە ئامادە دەکات، تۆ تەنها «Send» دەکەیت.
 */
class WhatsAppReceipt
{
    /** ژمارەی مۆبایل دەگۆڕێت بۆ فۆرماتی نێودەوڵەتی (عێراق: 07701234567 → 9647701234567) */
    public static function phone(?string $raw): ?string
    {
        $d = preg_replace('/\D+/', '', (string) $raw);
        if ($d === '') return null;

        if (str_starts_with($d, '00')) $d = substr($d, 2);
        if (str_starts_with($d, '964')) return $d;
        if (str_starts_with($d, '0')) return '964' . substr($d, 1);
        if (strlen($d) === 10 && $d[0] === '7') return '964' . $d;

        return $d; // ژمارەی دەرەوەی عێراق کە بە کۆدی وڵات نووسراوە
    }

    /** ئەگەر ژمارە نەبوو، واتساپ دەکرێتەوە و خۆت کڕیار هەڵدەبژێریت */
    public static function link(?string $phone, string $text): string
    {
        return 'https://wa.me/' . (self::phone($phone) ?? '') . '?text=' . rawurlencode($text);
    }

    protected static function money($v, string $cur): string
    {
        return $cur === 'USD'
            ? '$' . number_format((float) $v, 2)
            : number_format((float) $v) . ' د.ع';
    }

    protected static function qty($v): string
    {
        return rtrim(rtrim(number_format((float) $v, 3, '.', ''), '0'), '.');
    }

    protected static function shop(): string
    {
        $s = Setting::first();
        return $s->shop_name ?? 'کۆمپانیای بازرگانی';
    }

    protected static function footer(): string
    {
        $s = Setting::first();
        $lines = [];
        if (!empty($s->shop_phone)) $lines[] = '☎ ' . $s->shop_phone;
        $lines[] = $s->invoice_footer ?? 'سوپاس بۆ مامەڵەکەتان';
        return implode("\n", $lines);
    }

    /** دەقی قەرزی گشتی کڕیار (ئەگەر قەرزی هەبوو) */
    protected static function debtLines(?Customer $customer): array
    {
        if (!$customer || !method_exists($customer, 'currencySummary')) return [];
        $s = $customer->currencySummary();
        $parts = [];
        foreach (['USD', 'IQD'] as $cur) {
            $debt = (float) ($s[$cur]['debt'] ?? 0);
            if ($debt > 0) $parts[] = self::money($debt, $cur);
        }
        return $parts ? ['', '📌 *قەرزی گشتی ماوە:* ' . implode(' + ', $parts)] : [];
    }

    /** وەسڵی فرۆشتن */
    public static function sale($sale): string
    {
        $cur = ($sale->currency ?? 'IQD') === 'USD' ? 'USD' : 'IQD';
        $name = $sale->customer->name ?? ($sale->guest_name ?: 'کڕیاری گشتی');

        $L = [];
        $L[] = '🧾 *' . self::shop() . '*';
        $L[] = '*پسووڵەی فرۆشتن*';
        $L[] = '━━━━━━━━━━━━';
        $L[] = 'ژمارەی پسووڵە: ' . $sale->invoice_no;
        $L[] = 'بەروار: ' . $sale->created_at->format('Y-m-d H:i');
        $L[] = 'کڕیار: ' . $name;
        $L[] = 'جۆر: ' . ($sale->payment_type === 'debt' ? 'قەرز' : 'نەقد');
        $L[] = '━━━━━━━━━━━━';

        $max = 15; // دەقی زۆر درێژ لە لینکی واتساپ نایەتە ژوورەوە
        $i = 0;
        foreach ($sale->details as $d) {
            if ($i++ >= $max) {
                $L[] = '… و ' . ($sale->details->count() - $max) . ' کاڵای تر';
                break;
            }
            $L[] = '▪ ' . ($d->product->name ?? 'کاڵا')
                . "\n   " . self::qty($d->quantity) . ' ' . ($d->unit->name ?? '')
                . ' × ' . self::money($d->unit_price ?? ($d->line_total / ($d->quantity ?: 1)), $cur)
                . ' = ' . self::money($d->line_total, $cur);
        }

        $L[] = '━━━━━━━━━━━━';
        $disc = (float) ($sale->discount ?? 0);
        if ($disc > 0) {
            $L[] = 'کۆی کاڵاکان: ' . self::money($sale->total_amount + $disc, $cur);
            $L[] = 'داشکاندن: ' . self::money($disc, $cur);
        }
        $L[] = '💰 *کۆی گشتی: ' . self::money($sale->total_amount, $cur) . '*';
        if ($sale->payment_type === 'debt') {
            $L[] = 'پارەی دراو: ' . self::money($sale->paid_amount, $cur);
            $L[] = '⏳ *ماوە (قەرز): ' . self::money($sale->remaining_amount, $cur) . '*';
        }

        $L = array_merge($L, self::debtLines($sale->customer));
        $L[] = '';

        $L = array_merge($L, self::debtLines($sale->customer));
        
        // ─── ئەم چەند دێڕە زیاد بکە بۆ دانانی لینکی وەسڵەکە ───
        $L[] = '━━━━━━━━━━━━';
        $L[] = '📎 بۆ بینینی وەسڵەکەت بە وێنە/PDF کلیک لێرە بکە:';
        $L[] = route('sales.print', $sale->id); // ئەمە لینکە درێژەکە دروست دەکات خۆکارانە
        // ──────────────────────────────────────────────
        
        $L[] = '';
        $L[] = self::footer();

        return implode("\n", $L);

        $L[] = self::footer();

        return implode("\n", $L);
    }

    /** وەسڵی قەبز (وەرگرتنی پارە لە کڕیار) */
    public static function payment($payment, Customer $customer): string
    {
        $cur = Customer::normalizeCurrency($payment->currency);
        $date = $payment->payment_date ?? $payment->created_at->format('Y-m-d');

        $L = [];
        $L[] = '🧾 *' . self::shop() . '*';
        $L[] = '*وەسڵی قەبز (وەرگرتنی پارە)*';
        $L[] = '━━━━━━━━━━━━';
        $L[] = 'کڕیار: ' . $customer->name;
        $L[] = 'بەروار: ' . $date;
        $L[] = '✅ *بڕی وەرگیراو: ' . self::money($payment->amount, $cur) . '*';
        if (!empty($payment->note)) $L[] = 'تێبینی: ' . $payment->note;

        $s = method_exists($customer, 'currencySummary') ? $customer->currencySummary() : null;
        if ($s) {
            $rest = (float) ($s[$cur]['debt'] ?? 0);
            $L[] = '━━━━━━━━━━━━';
            $L[] = $rest > 0
                ? '⏳ *قەرزی ماوە: ' . self::money($rest, $cur) . '*'
                : '✔ حیسابت پاکە، قەرزی ' . ($cur === 'USD' ? 'دۆلار' : 'دینار') . ' نەماوە';
        }

        $L[] = '';
        $L[] = self::footer();

        return implode("\n", $L);
    }

    /** کەشفی حیساب (پوختە) */
    /** کەشفی حیساب (پوختە) */
    public static function statement(\App\Models\Customer $customer): string
    {
        $L = [];
        $L[] = '🧾 *' . self::shop() . '*';
        $L[] = '*کەشفی حیساب*';
        $L[] = '━━━━━━━━━━━━';
        $L[] = 'کڕیار: ' . $customer->name;

        $s = method_exists($customer, 'currencySummary') ? $customer->currencySummary() : [];
        foreach (['USD' => 'دۆلار', 'IQD' => 'دینار'] as $cur => $label) {
            if (empty($s[$cur])) continue;
            $L[] = '';
            $L[] = '*' . $label . '*';
            $L[] = 'کۆی کڕینەکان: ' . self::money($s[$cur]['purchases'] ?? 0, $cur);
            $L[] = 'پارەی دراو: ' . self::money($s[$cur]['paid'] ?? 0, $cur);
            $L[] = '⏳ ماوە: ' . self::money($s[$cur]['debt'] ?? 0, $cur);
        }

        // ─── زیادکردنی لینکی پارێزراو (بە پاسۆردی شاراوە) ───
        $L[] = '━━━━━━━━━━━━';
        $L[] = '📎 بۆ بینینی کەشفی حیسابەکەت بە تەواوی (وێنە یان PDF) کلیک لێرە بکە:';
        
        // دروستکردنی تۆکن بۆ ئەوەی کەس نەتوانێت لینکەکە دەستکاری بکات
        $token = substr(md5($customer->id . config('app.key')), 0, 10);
        $L[] = route('customers.statement', ['id' => $customer->id, 'token' => $token]); 
        // ───────────────────────────────

        $L[] = '';
        $L[] = self::footer();

        return implode("\n", $L);
    }
}