<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaleReturnDetail extends Model
{
    protected $fillable = [
        'sale_return_id',
        'product_id',
        'unit_id',
        'quantity',
        'unit_price',
        'subtotal',
        'condition_type',
    ];

    public function saleReturn()
    {
        return $this->belongsTo(SaleReturn::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
}