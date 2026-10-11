<?php

namespace App\Support;

/**
 * نووسینەکانی پسوولەی A4 (هەمووی لە ڕێکخستن دەگۆڕدرێن).
 * تەنها ئەوانەی گۆڕدراون لە settings.invoice_labels پاشەکەوت دەکرێن؛ ئەوانی تر بنەڕەتی دەبن.
 */
class InvoiceLabels
{
    public static function defaults(): array
    {
        return [
            // سەردێڕ و زانیاری وەسڵ
            'shop_address_label' => 'ناونیشان',
            'shop_phone_label'   => 'تەلەفۆن',
            'invoice_no'         => 'ژ.پسوولە',
            'cashier'            => 'فرۆشیار',
            'invoice_type'       => 'جۆری پسوولە',
            'type_cash'          => 'نەقد',
            'type_debt'          => 'قەرز',
            'customer_phone'     => 'ژ.موبایل',
            'customer_address'   => 'ناونیشانی کڕیار',
            // خشتەی کاڵاکان
            'col_no'    => 'ژ',
            'col_code'  => 'کۆد',
            'col_item'  => 'مادده',
            'col_unit'  => 'یەکە',
            'col_qty'   => 'دانە',
            'col_price' => 'نرخ',
            'col_total' => 'کۆ',
            'total_row'  => 'کۆ :',
            'weight_row' => 'کێشی گشتی',
            // بارەکانی خوارەوە
            'discount'       => 'داشکاندن',
            'after_discount' => 'کۆی پسوولە دوای داشکاندن',
            'paid'           => 'بڕی پارەی دراو',
            'remaining'      => 'ماوە (قەرز) لەم پسوولەیە',
            // قەرز
            'debt_before'   => 'قەرزی پێشوو',
            'debt_after'    => 'کۆی گشتی قەرز دوای ئەم پسوولەیە',
            'debt_usd_note' => 'کۆی گشتی قەرز بە دۆلار',
            // QR و واژوو
            'delivery'        => 'شوێنی گەیاندن',
            'qr_hint'         => 'QR سکان بکە بۆ کردنەوەی شوێنەکە لەسەر نەخشە',
            'sign_customer'   => 'واژووی کڕیار',
            'sign_accountant' => 'واژووی ژمێریار / کۆگا',
        ];
    }

    /** گرووپکردن بۆ فۆڕمی ڕێکخستن: ناوی گرووپ => [کلیلەکان] */
    public static function groups(): array
    {
        return [
            'سەردێڕ و زانیاری وەسڵ' => ['shop_address_label', 'shop_phone_label', 'invoice_no', 'cashier', 'invoice_type', 'type_cash', 'type_debt', 'customer_phone', 'customer_address'],
            'خشتەی کاڵاکان'          => ['col_no', 'col_code', 'col_item', 'col_unit', 'col_qty', 'col_price', 'col_total', 'total_row', 'weight_row'],
            'بارەکانی داشکاندن و پارە' => ['discount', 'after_discount', 'paid', 'remaining'],
            'قەرز'                    => ['debt_before', 'debt_after', 'debt_usd_note'],
            'QR و واژوو'              => ['delivery', 'qr_hint', 'sign_customer', 'sign_accountant'],
        ];
    }

    /** نووسینە کارا: بنەڕەتی + ئەوانەی لە ڕێکخستن گۆڕدراون */
    public static function forSetting($setting): array
    {
        $saved = [];
        $raw = $setting->invoice_labels ?? null;

        if (is_array($raw)) {
            $saved = $raw;
        } elseif (is_string($raw) && $raw !== '') {
            $saved = json_decode($raw, true) ?: [];
        }

        $saved = array_filter($saved, fn($v) => is_string($v) && trim($v) !== '');

        return array_merge(self::defaults(), array_intersect_key($saved, self::defaults()));
    }
}