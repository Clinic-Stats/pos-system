<?php

namespace App\Support;

use App\Models\Customer;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Support\Facades\URL;

/**
 * ناردنی وەسڵ بە واتساپ (بێ API و بێ تێچوون: لینکی wa.me)
 * دوگمەکە واتساپ دەکاتەوە و نامەکە ئامادە دەکات، تۆ تەنها «Send» دەکەیت.
 */
class WhatsAppReceipt
{
    /** زۆرترین ژمارەی کاڵا لە نامەکەدا (لینکی واتساپ دەقی زۆر درێژ قبوڵ ناکات) */
    protected const MAX_ITEMS = 10;

    /** ڕێکخستنەکان تەنها یەکجار لە داتابەیس دەخوێنرێتەوە (بۆ خێرایی لیستی فرۆشتن) */
    protected static $setting = false;

    protected static function setting()
    {
        if (self::$setting === false) {
            self::$setting = Setting::first();
        }
        return self::$setting;
    }

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

    /** کۆدی کورتی پارێزراو: ID + ١٢ پیتی HMAC (بە APP_KEY). کەس ناتوانێت ID بگۆڕێت */
    protected static function code(string $kind, $id): string
    {
        return $id . '-' . substr(hash_hmac('sha256', $kind . '|' . $id, (string) config('app.key')), 0, 12);
    }

    /** پشکنینی کۆد؛ ئەگەر دروست بوو ID دەگەڕێنێتەوە، ئەگەر نا null */
    public static function verify(string $kind, string $code): ?int
    {
        [$id, $sig] = array_pad(explode('-', $code, 2), 2, '');
        if ($id === '' || !ctype_digit($id)) return null;
        return hash_equals(self::code($kind, $id), $id . '-' . $sig) ? (int) $id : null;
    }

    /** لینکی پارێزراوی وەسڵی فرۆشتن (بێ پێویستی بە لۆگین، و کەس ناتوانێت دەستکاری بکات) */
    public static function saleUrl($sale): string
    {
        return url(route('share.sale', ['code' => self::code('sale', $sale->id)], false));
    }

    /** لینکی پارێزراوی کەشفی حیساب */
    public static function statementUrl(Customer $customer): string
    {
        return url(route('share.statement', ['code' => self::code('statement', $customer->id)], false));
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
        return self::setting()->shop_name ?? 'کۆمپانیای بازرگانی';
    }

    protected static function footer(): string
    {
        $s = self::setting();
        $lines = [];
        if (!empty($s->shop_phone)) $lines[] = '' . $s->shop_phone;
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
        return $parts ? ['', '*قەرزی گشتی ماوە:* ' . implode(' + ', $parts)] : [];
    }

    /** وەسڵی فرۆشتن */
    public static function sale($sale): string
    {
        $cur = ($sale->currency ?? 'IQD') === 'USD' ? 'USD' : 'IQD';
        $name = $sale->customer->name ?? ($sale->guest_name ?: 'کڕیاری گشتی');

        $L = [];
        $L[] = '*' . self::shop() . '*';
        $L[] = '*پسووڵەی فرۆشتن*';
        $L[] = '━━━━━━━━━━━━';
        $L[] = 'ژمارەی پسووڵە: ' . $sale->invoice_no;
        $L[] = 'بەروار: ' . $sale->created_at->format('Y-m-d H:i');
        $L[] = 'کڕیار: ' . $name;
        $L[] = 'جۆر: ' . ($sale->payment_type === 'debt' ? 'قەرز' : 'نەقد');
        $L[] = '━━━━━━━━━━━━';

        $max = self::MAX_ITEMS;
        $i = 0;
        foreach ($sale->details as $d) {
            if ($i++ >= $max) {
                $L[] = '… و ' . ($sale->details->count() - $max) . ' کاڵای تر (لە لینکی خوارەوە ببینە)';
                break;
            }
            $L[] = '- ' . ($d->product->name ?? 'کاڵا')
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
        $L[] = '*کۆی گشتی: ' . self::money($sale->total_amount, $cur) . '*';
        if ($sale->payment_type === 'debt') {
            $L[] = 'پارەی دراو: ' . self::money($sale->paid_amount, $cur);
            $L[] = '*ماوە (قەرز): ' . self::money($sale->remaining_amount, $cur) . '*';
        }

        // قەرزی گشتی کڕیار (تەنها یەکجار)
        $L = array_merge($L, self::debtLines($sale->customer));

        // لینکی پارێزراوی وەسڵەکە
        $L[] = '';
        $L[] = '━━━━━━━━━━━━';
        $L[] = 'بۆ بینینی وەسڵەکەت بە وێنە/PDF کلیک لێرە بکە:';
        $L[] = self::saleUrl($sale);

        $L[] = '';
        $L[] = self::footer();

        return implode("\n", $L);
    }

    /** وەسڵی قەبز (وەرگرتنی پارە لە کڕیار) */
    public static function payment($payment, Customer $customer): string
    {
        $cur = Customer::normalizeCurrency($payment->currency);
        $date = $payment->payment_date
            ? Carbon::parse($payment->payment_date)->format('Y-m-d')
            : $payment->created_at->format('Y-m-d');

        $L = [];
        $L[] = '*' . self::shop() . '*';
        $L[] = '*وەسڵی قەبز (وەرگرتنی پارە)*';
        $L[] = '━━━━━━━━━━━━';
        $L[] = 'کڕیار: ' . $customer->name;
        $L[] = 'بەروار: ' . $date;
        $L[] = '*بڕی وەرگیراو: ' . self::money($payment->amount, $cur) . '*';
        if (!empty($payment->note)) $L[] = 'تێبینی: ' . $payment->note;

        $s = method_exists($customer, 'currencySummary') ? $customer->currencySummary() : null;
        if ($s) {
            $rest = (float) ($s[$cur]['debt'] ?? 0);
            $L[] = '━━━━━━━━━━━━';
            $L[] = $rest > 0
                ? '*قەرزی ماوە: ' . self::money($rest, $cur) . '*'
                : 'حیسابت پاکە، قەرزی ' . ($cur === 'USD' ? 'دۆلار' : 'دینار') . ' نەماوە';
        }

        $L[] = '';
        $L[] = self::footer();

        return implode("\n", $L);
    }

    /** کەشفی حیساب (پوختە + لینک) */
    public static function statement(Customer $customer): string
    {
        $L = [];
        $L[] = '*' . self::shop() . '*';
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
            $L[] = 'ماوە: ' . self::money($s[$cur]['debt'] ?? 0, $cur);
        }

        // لینکی پارێزراو (واژووکراو، بێ پێویستی بە لۆگین)
        $L[] = '';
        $L[] = '━━━━━━━━━━━━';
        $L[] = 'بۆ بینینی کەشفی حیسابەکەت بە تەواوی (وێنە یان PDF) کلیک لێرە بکە:';
        $L[] = self::statementUrl($customer);

        $L[] = '';
        $L[] = self::footer();

        return implode("\n", $L);
    }
}