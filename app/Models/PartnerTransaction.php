<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PartnerTransaction extends Model
{
    // ڕێگەدان بە پڕکردنەوەی هەموو ستوونەکان (date, amount, type, note, partner_id)
    protected $guarded = [];

    // شێوازی فۆرماتی بەروار
    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }
}