<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaleReturn extends Model
{
    protected $fillable = [
        'return_no',
        'customer_id',
        'user_id',
        'total_amount',
        'refund_type',
        'notes',
        'created_at',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function details()
    {
        return $this->hasMany(SaleReturnDetail::class);
    }
    public function user()
{
    return $this->belongsTo(User::class);
}
}