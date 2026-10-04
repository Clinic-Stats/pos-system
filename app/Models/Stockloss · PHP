<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockLoss extends Model
{
    protected $fillable = [
        'product_id', 'quantity', 'unit_cost_usd', 'total_cost_usd', 'reason', 'note',
        'source', 'sale_return_id', 'customer_id', 'user_id', 'loss_date',
    ];

    protected $casts = ['loss_date' => 'datetime'];

    public const REASONS = [
        'expired' => 'بەسەرچوو',
        'damaged' => 'تێکچوو / شکاو',
        'lost'    => 'ونبوو / دزرا',
        'other'   => 'هۆکاری تر',
    ];

    public function product()    { return $this->belongsTo(Product::class); }
    public function customer()   { return $this->belongsTo(Customer::class); }
    public function user()       { return $this->belongsTo(User::class); }
    public function saleReturn() { return $this->belongsTo(SaleReturn::class); }
}