<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashHandover extends Model
{
    protected $fillable = [
        'receipt_no',
        'mandub_id',
        'received_by',
        'amount',
        'note',
        'handover_date',
    ];

    protected $casts = [
        'handover_date' => 'datetime',
    ];

    // مەندووبی تەسلیمکار
    public function mandub()
    {
        return $this->belongsTo(User::class, 'mandub_id');
    }

    // ئەو کەسەی پارەکەی وەرگرتووە (ئەدمین)
    public function receiver()
    {
        return $this->belongsTo(User::class, 'received_by');
    }
}
