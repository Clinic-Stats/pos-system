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

    // پەیوەندی بە وەسڵەکانەوە (Sales)
    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    // پەیوەندی بە پارەدان و گەڕاندنەوەی قەرز (Payments)
    public function payments()
    {
        return $this->hasMany(CustomerPayment::class);
    }

    // هەژمارکردنی کۆی گشتی قەرزی ماوەی کڕیار
    public function totalDebt()
    {
        $totalSalesDebt = $this->sales()->sum('remaining_amount');
        $totalPaidLater = $this->payments()->sum('amount');
        
        return max(0, $totalSalesDebt - $totalPaidLater);
    }
    public function returns()
{
    return $this->hasMany(SaleReturn::class);
}
}