<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Partner extends Model
{
    protected $guarded = [];

    public function transactions()
    {
        return $this->hasMany(PartnerTransaction::class)->orderBy('created_at', 'asc')->orderBy('id', 'asc');
    }

    public function getInitialCapitalAttribute()
    {
        return (float) ($this->capital_amount ?? $this->capital ?? 0);
    }

    public function getShareAttribute()
    {
        return (float) ($this->share_percentage ?? $this->share_percent ?? 0);
    }

    public function getCurrentBalanceAttribute()
    {
        $deposits = $this->transactions->where('type', 'deposit')->sum('amount');
        $withdraws = $this->transactions->where('type', 'withdraw')->sum('amount');

        return $this->initial_capital + $deposits - $withdraws;
    }
}